<?php
/* This class handles password encryption and decryption
 * Created by Jordan Perkins
*/
namespace AppBundle\Form\Model;

class Crypt
{

  protected $hash;

  public function encrypt($text)
  {
     $method = "AES-256-CBC";
     $iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);
     $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

     $encrypted = openssl_encrypt($text, $method, $this->getHash(), 0, $iv);

     return base64_encode($iv . $encrypted);
   }

   public function decrypt($text)
   {
     $text = base64_decode($text);

     $method = "AES-256-CBC";
     $iv_size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CBC);
     $iv = substr($text, 0, $iv_size);

     $decrypted = openssl_decrypt(substr($text, $iv_size), $method, $this->getHash(), 0, $iv);

     return $decrypted;
   }

   public function __construct ($hash) {
     $this->hash = $hash;
   }

   public function getHash()
   {
       return $this->hash;
   }

   public function setHash($hash)
   {
       $this->hash = $hash;

       return $this;
   }

}
