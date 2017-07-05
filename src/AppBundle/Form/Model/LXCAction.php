<?php
/* This class is form control for allowing server actions on LXC servers.
 * Created by Jordan Perkins
*/
namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Log;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Form\Model\Crypt;

class LXCAction
{
     protected $action;

     protected $value;

     protected $server;

     protected $node;

     protected $os;

     protected $user;

     protected $request;

     protected $hash;

     /**
      * @Assert\IsTrue(message = "Validation fail.")
      */
      // Function that is validated to ensure that value data is clean.
      public function isValid()
      {
        if (strpos($this->getValue(), '&') !== false || strpos($this->getValue(), "'") !== false || strpos($this->getValue(), '"') !== false || strpos($this->getValue(), '\\') !== false || strpos($this->getValue(), '/') !== false) {
          return false;
        }
        return true;
      }


     // Function for handling panel requests
     public function handle() {
       try {
         $status = $this->getNode()->command("get", $this->getPath()."/status/current", $this->getHash());
       } catch (\Exception $e) {
         $error = "Host node is down";
         $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), $this->getValue(), $this->getServer()->getId(), $this->getUser()->getId(), false, json_encode($error));
         return [[false, $error], $log];
       }

       $action = $this->getAction();
       $actions = array("boot", "shutdown", "restart", "hostname", "nameserver", "password", "reinstall", "status", "graph", "resize");
       if (!in_array($this->getAction(), $actions)) {
         return [[false, "Action is invalid"], null];
       }

       $result = $this->$action($status);
       $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), $this->getValue(), $this->getServer()->getId(), $this->getUser()->getId(), $result[0], json_encode($result[1]));
       return [$result, $log];
     }

     // All of the action code is below.

     private function boot($status) {
       if ($status[1]["status"] == "stopped") {
         return $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       } else {
         return [false, "Container already booted"];
       }
     }

     private function shutdown($status) {
       if ($status[1]["status"] == "running") {
         if ($this->getValue() == "on") {
           return $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         } else {
           return $this->getNode()->command("create", $this->getPath()."/status/shutdown", $this->getHash());
         }
       } else {
         return [false, "Container already stopped"];
       }
     }

     private function restart($status) {
       if ($status[1]["status"] == "running") {
         $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         return $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       } else {
         return [false, "Container not running"];
       }
     }

     private function hostname($status) {
       if ($this->isValidHostname($this->getValue())) {
         $result = $this->getNode()->command("set", $this->getPath()."/config", $this->getHash(), ['hostname' => $this->getValue()]);
         $this->getServer()->setHostname($this->getValue());
         return $result;
       } else {
         return [false, "Hostname is invalid"];
       }
     }

     private function nameserver($status) {
       if ($this->isValidNameserver($this->getValue())) {
         if ($status[1]["status"] == "running") {
           $result = $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
           if (!$result[0]) {
             return $result;
           }
           $boot = true;
         }
         $result = $this->getNode()->command("set", $this->getPath()."/config", $this->getHash(), ['nameserver' => $this->getValue()]);
         $this->getServer()->setNameserver($this->getValue());
         if (isset($boot)) {
           $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
         }
         return $result;
       } else {
         return [false, "Nameservers are invalid"];
       }
     }

     private function password($status) {
       $password = $this->getValue();
       $this->setValue('');
       if (strlen($password) >= 5) {
         if ($status[1]["status"] == "stopped") {
           $result = $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
           if (!$result[0]) {
             return $result;
           }
           $shutdown = true;
         }
         $result = $this->getNode()->command("set", $this->getPath()."/rootpass", $this->getHash(), ['password' => $password]);
         $crypt = new Crypt($this->getHash());
         $rootpass = $crypt->encrypt($password);
         $this->getServer()->setRootpass($rootpass);
         if (isset($shutdown)) {
           $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         }
         return $result;
       } else {
         return [false, "Password is too short"];
       }
     }

     private function reinstall($status) {
       $os = $this->fetchOS($this->getValue());
       $config = $this->getNode()->command("get", $this->getPath()."/config", $this->getHash());
       $server = $this->getServer();
       if ($os != null) {
         if ($status[1]["status"] == "running") {
           $result = $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
           if (!$result[0]) {
             return $result;
           }
           sleep(3);
         }
         $crypt = new Crypt($this->getHash());
         $rootpass = $crypt->decrypt($server->getRootpass());
         $this->getNode()->command("delete", $this->getPath(), $this->getHash());
         $newdata = [
           'vmid' => $server->getCtid(),
           'ostemplate' => $os->getStorage().':vztmpl/'.$os->getFile().'.'.$os->getExtension(),
           'cmode' => $server->getCmode(),
           'console' => (int)$server->getConsole(),
           'cores' => $server->getCpu(),
           'cpulimit' => $server->getCpulimit(),
           'cpuunits' => $server->getCpuunits(),
           'hostname' => $server->getHostname(),
           'memory' => $server->getRam(),
           'nameserver' => $server->getNameserver(),
           'onboot' => (int)$server->getOnboot(),
           'password' => $rootpass,
           'searchdomain' => $server->getSearch(),
           'storage' => $server->getStorage(),
           'swap' => $server->getSwap(),
           'tty' => $server->getTty(),
           'unprivileged' => (int)$server->getUnprivileged(),
         ];
         if (isset($config[1])) {
           foreach ($config[1] as $key => $value) {
             if (strpos($key, 'net') === 0) {
               $newdata[$key] = $value;
             }
           }
         }
         $result = $this->getNode()->command("create", "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType(), $this->getHash(), $newdata);
         if (!$result[0]) {
           return $result;
         }
         sleep(3);
         $result = $this->getNode()->command("set", $this->getPath()."/resize", $this->getHash(), ['disk' => 'rootfs', 'size' => $server->getDisk().'G']);
         $server->setOs($os->getName());
         return $result;
       } else {
         return [false, "OS is invalid"];
       }
     }

     public function status($status)
     {
       $result = $status;
       if ($result[0] && $result[1]) {
         $result = $result[1];
         $result[0] = true;
         $result[1] = null;
         $result["os"] = $this->getServer()->getOs();
         if ($this->getServer()->getSuspended()) {
           $result["status"] = "suspended";
         }
         $result["node"] = $this->getNode()->getName();
         $result["mem"] = round($result["mem"]/(1024*1024), 0);
         $result["swap"] = round($result["swap"]/(1024*1024), 0);
         $result["disk"] = round($result["disk"]/(1024*1024*1024), 1);
         $result["cpu"] = round($result["cpu"]*100, 1);
         $result["availablemem"] = $this->getServer()->getRam();
         $result["availableswap"] = $this->getServer()->getSwap();
         $result["availabledisk"] = $this->getServer()->getDisk();
         $result["ram_percent"] = round($result["mem"]*100 / $this->getServer()->getRam(), 0);
         $result["swap_percent"] = round($result["swap"]*100 / $this->getServer()->getSwap(), 0);
         $result["disk_percent"] = round($result["disk"]*100 / $this->getServer()->getDisk(), 0);
         $result["ip"] = $this->getServer()->getIp();
         $result["nameserver"] = $this->getServer()->getNameserver();
         $dtF = new \DateTime('@0');
         $dtT = new \DateTime("@".$result["uptime"]);
         $result["uptime"] = $dtF->diff($dtT)->format('%a days, %h hours, %i mins');
         return $result;
       } else {
         return [false, "Could not fetch status"];
       }
     }

     public function graph($status)
     {
       $type = $this->getValue()[0];
       $period = $this->getValue()[1];
       $result = $this->getNode()->command("get", $this->getPath()."/rrd", $this->getHash(), ['ds' => $type, 'timeframe' => $period], true);
       return $result;
     }

     public function resize($status)
     {
       if (!$this->getUser()->getIsAdmin()) {
         return [false, "Permission Denied"];
       }

       if ($status[1]["status"] == "running") {
         $result = $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         if (!$result[0]) {
           return $result;
         }
         $boot = true;
      }
       $result = $this->getNode()->command("set", $this->getPath()."/resize", $this->getHash(), ['disk' => 'rootfs', 'size' => $this->getServer()->getDisk().'G']);
       if (isset($boot)) {
         sleep(3);
         $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       }
       if (!$result[0] && isset($result[1]["_root"])) {
         return [false, "Disk is already correct size"];
       }
       return $result;
     }


    // Constructor
    public function __construct ($params, $hash, $action = null, $value = null) {
      $this->server = $params[0];
      $this->node = $params[1];
      $this->os = $params[2];
      $this->request = $params[4];
      $this->hash = $hash;
      $this->user = $params[3];
      if ($action != null) {
        $this->action = $action;
      }
      if ($value != null) {
        $this->value = $value;
      }
    }

    public function isValidHostname($hostname) {
      return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $hostname) //valid chars check
       && preg_match("/^.{1,253}$/", $hostname) //overall length check
       && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $hostname)); //length of each label
    }

    public function isValidNameserver($nameservers) {
      $values = explode(' ', $nameservers);
      $return = true;
      foreach ($values as $value) {
        if (filter_var($value, FILTER_VALIDATE_IP) === false) {
          $return = false;
        }
      }
      return $return;
    }

    public function fetchOS($newos) {
      foreach ($this->getOs() as $os) {
        if ($os->getFile() == $newos) {
          return $os;
        }
      }
      return null;
    }

     // Getter / Setter Methods
     public function setAction($action)
     {
         $this->action = $action;

         return $this;
     }

     /**
      * Get action
      *
      * @return string
      */
     public function getAction()
     {
         return $this->action;
     }

     public function setValue($value)
     {
         $this->value = $value;

         return $this;
     }

     public function getValue()
     {
         return $this->value;
     }
     public function setServer($server)
     {
         $this->server = $server;

         return $this;
     }

     public function getServer()
     {
         return $this->server;
     }
     public function setOs($os)
     {
         $this->os = $os;

         return $this;
     }

     public function getOs()
     {
         return $this->os;
     }
     public function setNode($node)
     {
         $this->node = $node;

         return $this;
     }

     public function getNode()
     {
         return $this->node;
     }
     public function setUser($user)
     {
         $this->user = $user;

         return $this;
     }

     public function getUser()
     {
         return $this->user;
     }
     public function setRequest($request)
     {
         $this->request = $request;

         return $this;
     }

     public function getRequest()
     {
         return $this->request;
     }

     public function setHash($hash)
     {
         $this->hash = $hash;

         return $this;
     }

     public function getHash()
     {
         return $this->hash;
     }

     public function getPath() {
       return "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType()."/".$this->getServer()->getCtid();
     }
}
