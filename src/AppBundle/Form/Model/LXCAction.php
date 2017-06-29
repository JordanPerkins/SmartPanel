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
       $status = $this->getNode()->command("get", $this->getPath()."/status/current", $this->getHash());
       switch ($this->getAction()) {
         case "boot":
           $result = $this->boot($status); break;
         case "shutdown":
           $result = $this->shutdown($status); break;
         case "restart":
           $result = $this->restart($status); break;
         case "hostname":
           $result = $this->hostname($status); break;
         case "nameserver":
           $result = $this->nameserver($status); break;
         case "password":
           $result = $this->password($status); break;
         case "tuntap":
           $result = $this->tuntap($status); break;
         case "reinstall":
           $result = $this->reinstall($status); break;
      }
      $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), $this->getValue(), $this->getServer()->getId(), $this->getUser()->getId(), $result[0], json_encode($result[1]));
      return [$result[0], $log];
     }

     // All of the action code is below.

     private function boot($status) {
       if ($status[1]["status"] == "stopped") {
         $result = $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       } else {
         $result = [false, "Container already booted"];
       }
       return $result;
     }

     private function shutdown($status) {
       if ($status[1]["status"] == "running") {
         $result = $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
       } else {
         $result = [false, "Container already stopped"];
       }
       return $result;
     }

     private function restart($status) {
       if ($status[1]["status"] == "running") {
         $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         $result = $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       } else {
         $result = [false, "Container not running"];
       }
       return $result;
     }

     private function hostname($status) {
       if ($this->isValidHostname($this->getValue())) {
         $result = $this->getNode()->command("set", $this->getPath()."/config", $this->getHash(), ['hostname' => $this->getValue()]);
         $this->getServer()->setHostname($this->getValue());
       } else {
         $result = [false, "Hostname is invalid"];
       }
       return $result;
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
       } else {
         $result = [false, "Nameservers are invalid"];
       }
       return $result;
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
       } else {
         $result = [false, "Password is too short"];
       }
       return $result;
     }

     private function tuntap($status) {
       if ($this->getValue() == "on") {
         $this->getServer()->setTuntap(true);
         $result = $this->getNode()->command("create", $this->getPath()."/tuntap", $this->getHash());
       } else {
         $this->getServer()->setTuntap(false);
         $result = $this->getNode()->command("create", $this->getPath()."/tuntapoff", $this->getHash());
       }
       return $result;
     }

     private function reinstall($status) {
       $config = $this->getNode()->command("get", $this->getPath()."/config", $this->getHash());
       $server = $this->getServer();
       if ($this->isValidOS($this->getValue())) {
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
           'ostemplate' => 'local:vztmpl/'.$this->getValue().'.tar.gz',
           'cmode' => $server->getCmode(),
           'console' => (int)$server->getConsole(),
           'cores' => $server->getCpu(),
           'cpulimit' => $server->getCpulimit(),
           'cpuunits' => $server->getCpuunits(),
           'hostname' => $server->getHostname(),
           'memory' => $server->getRam(),
           'nameserver' => $server->getNameserver(),
           'onboot' => 1,
           'password' => $rootpass,
           'searchdomain' => 'budgetnode',
           'storage' => 'local-lvm',
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
         $this->setOSName($this->getValue());
       } else {
         $result = [false, "OS is invalid"];
       }
       return $result;
     }

    // Constructor
    public function __construct ($server, $node, $os, $user, $request, $hash) {
      $this->server = $server;
      $this->node = $node;
      $this->os = $os;
      $this->request = $request;
      $this->hash = $hash;
      $this->user = $user;
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

    public function isValidOS($newos) {
      $valid = false;
      foreach ($this->getOs() as $os) {
        if ($os->getFile() == $newos) {
          $valid = true;
        }
      }
      return $valid;
    }

    public function setOSName($newos) {
      foreach ($this->getOs() as $os) {
        if ($os->getFile() == $newos) {
          $this->getServer()->setOs($os->getName());
        }
      }
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
