<?php
/* This class is form control for allowing server actions on OpenVZ servers.
 * Created by Jordan Perkins
*/
namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Log;
use Symfony\Component\Config\Definition\Exception\Exception;

class OpenVZAction
{
     protected $action;

     protected $value;

     protected $server;

     protected $node;

     protected $os;

     protected $user;

     protected $request;

     /**
      * @Assert\IsTrue(message = "Validation fail.")
      */
      // Function that is validated to ensure that value data is clean.
      public function isValid()
      {
        if (strpos($this->getValue(), '&') !== false || strpos($this->getValue(), "'") !== false || strpos($this->getValue(), '"') !== false || strpos($this->getValue(), '\\') !== false || strpos($this->getValue(), '/') !== false) {
          return false;
        } else if ($this->getAction() == "hostname") {
          return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $this->getValue()) //valid chars check
           && preg_match("/^.{1,253}$/", $this->getValue()) //overall length check
           && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $this->getValue())); //length of each label
        }
        return true;
      }


     // Function for handling panel requests
     public function handle() {
        $status = $this->getNode()->command("get", "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType()."/".$this->getServer()->getCtid()."/status/current");
       if ($this->getAction() == "boot") {
         if ($status[1]["status"] == "stopped") {
           $result = $this->getNode()->command("create", "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType()."/".$this->getServer()->getCtid()."/status/start");
         } else {
           $result = [false, null];
         }
       }
       if ($this->getAction() == "shutdown") {
         if ($status[1]["status"] == "running") {
           $result = $this->getNode()->command("create", "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType()."/".$this->getServer()->getCtid()."/status/stop");
         } else {
           $result = [false, null];
         }
       }
       if ($this->getAction() == "restart") {
         if ($status[1]["status"] == "running") {
           $this->getNode()->command("create", "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType()."/".$this->getServer()->getCtid()."/status/stop");
           $result = $this->getNode()->command("create", "/nodes/".$this->getNode()->getIdentifier()."/".$this->getServer()->getType()."/".$this->getServer()->getCtid()."/status/start");
         } else {
           $result = [false, null];
         }
       }
      $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), $this->getValue(), $this->getServer()->getId(), $this->getUser()->getId(), (int)$result[0]);
      return [$result[0], $log];
     }

    // Constructor
    public function __construct ($server, $node, $os, $user, $request) {
      $this->server = $server;
      $this->node = $node;
      $this->os = $os;
      $this->user = $user;
      $this->request = $request;
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
}
