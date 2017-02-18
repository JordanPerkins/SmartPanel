<?php
/* This class is form control for allowing server actions on OpenVZ servers.
 * Created by Jordan Perkins
*/
namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Log;

class OpenVZAction
{
  /**
    * @Assert\Choice(
    *     choices = { "start", "restart", "stop", "hostname", "password", "tuntap_enable", "tuntap_disable", "fuse_enable", "fuse_disable", "reinstall", "mainip"},
    *     message = "Invalid action."
    * )
    */
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

       $result = $this->getNode()->command($this->getAction(), $this->getServer()->getType(), [
         'ctid' => $this->getServer()->getCtid(),
         'value' => $this->getValue(),
         ])["error"];
         //Add to event log
         if ($this->getAction() == "password") {
           $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), null, $this->getServer()->getId(), $this->getUser()->getId(), 1-$result);
         } else {
             $log = new Log($this->getAction(), new \DateTime("now"), $this->getRequest()->getClientIp(), $this->getValue(), $this->getServer()->getId(), $this->getUser()->getId(), 1-$result);
         }
      if ($result == 0) {
        // Update the server entity
        if ($this->getAction() == "hostname") {
          $this->getServer()->setHostname($this->getValue());
        }
        if ($this->getAction() == "mainip") {
          $this->getServer()->setIp($this->getValue());
        }
        if ($this->getAction() == "reinstall") {
          foreach ($this->getOs() as $os) {
            if ($os->getFile() == $this->getValue()) {
              $name = $os->getName();
            }
          }
          $this->getServer()->setOs($name);
        }
        if ($this->getAction() == "tuntap_enable" || $this->getAction() == "tuntap_disable" || $this->getAction() == "fuse_enable" || $this->getAction() == "fuse_disable") {
          $info = explode('_', $this->getAction());
          $method = 'set'.ucfirst($info[0]);
          if ($info[1] == "enable") {
            $this->getServer()->$method(true);
          } else {
            $this->getServer()->$method(false);
          }
        }
        return [$this->getServer(), $log];
      } else {
        return [null, $log];
      }
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
