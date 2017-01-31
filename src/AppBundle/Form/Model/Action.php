<?php
/* This class is form control for allowing server actions.
 * Created by Jordan Perkins
*/
namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class Action
{
  /**
    * @Assert\Choice(
    *     choices = { "start", "restart", "stop", "hostname", "password", "tuntap_enable", "tuntap_disable"},
    *     message = "Invalid action."
    * )
    */
     protected $action;

     protected $value;

     /**
      * @Assert\IsTrue(message = "Validation fail.")
      */
      // Function that is validated to ensure that value data is clean.
      public function isValid()
      {
        if (strpos($this->getValue(), '&') !== false || strpos($this->getValue(), "''") !== false || strpos($this->getValue(), '"') !== false) {
          return false;
        } else if ($this->getAction() == "hostname") {
          return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $this->getValue()) //valid chars check
           && preg_match("/^.{1,253}$/", $this->getValue()) //overall length check
           && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $this->getValue())); //length of each label
        }
        return true;
      }


     // Function for handling panel requests
     public function handle($server, $node) {

       $result = $node->command($this->getAction(), $server->getType(), [
         'ctid' => $server->getCtid(),
         'value' => $this->getValue(),
         ])["error"];
      if ($result == 0) {
        return 1;
      } else {
        return 0;
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
}
