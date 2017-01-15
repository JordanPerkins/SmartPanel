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
    *     choices = { "start", "restart", "stop" },
    *     message = "Invalid action."
    * )
    */
     protected $action;

     // Function for handling panel requests
     public function handle($server, $node) {

       if ($this->getAction() == "start" || $this->getAction() == "stop" || $this->getAction() == "restart") {
         $result = $node->command($this->getAction(), $server->getType(), [
               'ctid' => $server->getCtid(),
             ])["error"];
         if ($result == 0) {
           return 1;
         } else {
           return 0;
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
}
