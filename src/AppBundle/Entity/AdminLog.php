<?php
/* Log entity created by Jordan Perkins
 * This is used to log user actions
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * @ORM\Table(name="app_adminlogs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AdminLogRepository")
 */
class AdminLog extends Controller
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $action;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $error;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $value;

    /**
     * @ORM\Column(type="integer", length=30)
     */
    private $service;
    /**
     * @ORM\Column(type="integer", length=20)
     */

     private $uid;
     /**
      * @ORM\Column(type="boolean")
      */

     private $result;

     public function __construct ($action, $datetime, $ip, $value, $service, $uid, $result, $error) {
       $this->action = $action;
       $this->datetime = $datetime;
       $this->ip = $ip;
       if ($value != null) {
         $this->value = $value;
       } else {
         $this->value = "";
       }
       $this->service = $service;
       $this->uid = $uid;
       $this->result = $result;
       if (!$result) {
         $this->error = $error;
       } else {
         $this->error = "none";
       }
     }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return Log
     */
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

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return Log
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Log
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Log
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set service
     *
     * @param integer $service
     *
     * @return Log
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return integer
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     *
     * @return Log
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set result
     *
     * @param integer $result
     *
     * @return Log
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return integer
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set error
     *
     * @param string $error
     *
     * @return Log
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
