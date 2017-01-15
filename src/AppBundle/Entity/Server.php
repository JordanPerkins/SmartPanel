<?php
/* Server entity created by Jordan Perkins
 * This is used to store individual user virtual servers
 * There is a function which returns the percentage resource usage
 * based on the live values in the input.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="app_servers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServerRepository")
 */
class Server
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $uid;

    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $nid;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $hostname;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"openvz", "kvm"})
     */
    private $type;
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $ctid;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $uuid;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $disk;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $ram;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $bandwidth;
    /**
     * @ORM\Column(type="string", length=30)
     */
    private $ip;

    // Percentage Usage calculator
    public function getPercent($status)
    {
        $result = array();
        if ($this->getRam() == 0 || $this->getDisk() == 0) {
          return false;
        } else {
          $result["ram"] = round($status["ram"]*100 / $this->getRam(), 0);
          $result["disk"] = round($status["disk"]*100 / $this->getDisk(), 0);
          return $result;
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
     * Set uid
     *
     * @param integer $uid
     *
     * @return Server
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
     * Set hostname
     *
     * @param string $hostname
     *
     * @return Server
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Server
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set nid
     *
     * @param integer $nid
     *
     * @return Server
     */
    public function setNid($nid)
    {
        $this->nid = $nid;

        return $this;
    }

    /**
     * Get nid
     *
     * @return integer
     */
    public function getNid()
    {
        return $this->nid;
    }

    /**
     * Set ctid
     *
     * @param string $ctid
     *
     * @return Server
     */
    public function setCtid($ctid)
    {
        $this->ctid = $ctid;

        return $this;
    }

    /**
     * Get ctid
     *
     * @return string
     */
    public function getCtid()
    {
        return $this->ctid;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return Server
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }


    /**
     * Set disk
     *
     * @param integer $disk
     *
     * @return Server
     */
    public function setDisk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Get disk
     *
     * @return integer
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * Set ram
     *
     * @param integer $ram
     *
     * @return Server
     */
    public function setRam($ram)
    {
        $this->ram = $ram;

        return $this;
    }

    /**
     * Get ram
     *
     * @return integer
     */
    public function getRam()
    {
        return $this->ram;
    }

    /**
     * Set bandwidth
     *
     * @param integer $bandwidth
     *
     * @return Server
     */
    public function setBandwidth($bandwidth)
    {
        $this->bandwidth = $bandwidth;

        return $this;
    }

    /**
     * Get bandwidth
     *
     * @return integer
     */
    public function getBandwidth()
    {
        return $this->bandwidth;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Server
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
}
