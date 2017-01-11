<?php
/* This class is form control for allowing password updates.
 * Created by Jordan Perkins
 * It is simply used as temporary storage for the form.
 * The Validator is used alongside it.
*/
namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password"
     * )
     */
     protected $password;

    /**
     * @Assert\Length(
     *     min = 8,
     *     minMessage = "Password should by at least 8 chars long"
     * )
     * @Assert\Regex(
     *     pattern="@[A-Z]@",
     *     match=true,
     *     message="Your password must contain uppercase characters."
     * )
     * @Assert\Regex(
     *     pattern="@[a-z]@",
     *     match=true,
     *     message="Your password must contain lowercase characters."
     * )
     * @Assert\Regex(
     *     pattern="@[0-9]@",
     *     match=true,
     *     message="Your password must contain numbers."
     * )
     */
     protected $newPassword;

     protected $verifyPassword;

     /**
      * @Assert\IsTrue(message = "Your passwords did not match.")
      */
      // Function that is validated to ensure the two passwords the user entered match.
      public function isPasswordMatching()
      {
        return $this->newPassword == $this->verifyPassword;
      }

      public function getPassword()
      {
          return $this->password;
      }

      public function getNewPassword()
      {
          return $this->newPassword;
      }

      public function getVerifyPassword()
      {
          return $this->verifyPassword;
      }

      public function setPassword($password)
      {
          $this->password = $password;

          return $this;
      }

      public function setNewPassword($password)
      {
          $this->newPassword = $password;

          return $this;
      }

      public function setVerifyPassword($password)
      {
          $this->verifyPassword = $password;

          return $this;
      }

}
