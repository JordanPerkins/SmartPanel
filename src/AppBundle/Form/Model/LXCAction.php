<?php
/* This class is form control for allowing server actions on LXC servers.
 * Created by Jordan Perkins
*/
namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Log;
use Symfony\Component\Config\Definition\Exception\Exception;

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
        if ($this->getAction() == "hostname") {
          return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $this->getValue()) //valid chars check
           && preg_match("/^.{1,253}$/", $this->getValue()) //overall length check
           && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $this->getValue())); //length of each label
        }
        if ($this->getAction() == "nameserver") {
          $values = explode(' ', $this->getValue());
          $return = true;
          foreach ($values as $value) {
            if (filter_var($value, FILTER_VALIDATE_IP) === false) {
              $return = false;
            }
          }
          return $return;
        }
        return true;
      }


     // Function for handling panel requests
     public function handle() {
       $status = $this->getNode()->command("get", $this->getPath()."/status/current", $this->getHash());
       switch ($this->getAction()) {
         case "boot":
          $result = $this->boot($status);
          break;
        case "shutdown":
          $result = $this->shutdown($status);
          break;
        case "restart":
          $result = $this->restart($status);
          break;
        case "hostname":
          $result = $this->hostname($status);
          break;
        case "nameserver":
          $result = $this->nameserver($status);
          break;
        case "password":
          $result = $this->password($status);
          break;
        case "tuntap":
          $result = $this->tuntap($status);
          break;
      }
      $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), $this->getValue(), $this->getServer()->getId(), $this->getUser()->getId(), (int)$result[0]);
      return [$result[0], $log];
     }

     // All of the action code is below.

     private function boot($status) {
       if ($status[1]["status"] == "stopped") {
         $result = $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       } else {
         $result = [false, null];
       }
       return $result;
     }

     private function shutdown($status) {
       if ($status[1]["status"] == "running") {
         $result = $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
       } else {
         $result = [false, null];
       }
       return $result;
     }

     private function restart($status) {
       if ($status[1]["status"] == "running") {
         $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         $result = $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       } else {
         $result = [false, null];
       }
       return $result;
     }

     private function hostname($status) {
       $result = $this->getNode()->command("set", $this->getPath()."/config", $this->getHash(), ['hostname' => $this->getValue()]);
       $this->getServer()->setHostname($this->getValue());
       return $result;
     }

     private function nameserver($status) {
       if ($status[1]["status"] == "running") {
         $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
         $boot = true;
       }
       $result = $this->getNode()->command("set", $this->getPath()."/config", $this->getHash(), ['nameserver' => $this->getValue()]);
       $this->getServer()->setNameserver($this->getValue());
       if (isset($boot)) {
         $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
       }
       return $result;
     }

     private function password($status) {
       if ($status[1]["status"] == "stopped") {
         $this->getNode()->command("create", $this->getPath()."/status/start", $this->getHash());
         $shutdown = true;
       }
       $result = $this->getNode()->command("set", $this->getPath()."/rootpass", $this->getHash(), ['password' => $this->getValue()]);
       $this->setValue('');
       if (isset($shutdown)) {
         $this->getNode()->command("create", $this->getPath()."/status/stop", $this->getHash());
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

    // Constructor
    public function __construct ($server, $node, $os, $user, $request, $hash) {
      $this->server = $server;
      $this->node = $node;
      $this->os = $os;
      $this->user = $user;
      $this->request = $request;
      $this->hash = $hash;
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
