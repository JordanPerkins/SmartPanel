<?php
/* Server entity created by Jordan Perkins
 * This is used to store individual user virtual servers
 * There is a function which returns the percentage resource usage
 * based on the live values in the input.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * @ORM\Table(name="app_servers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServerRepository")
 */
class Server extends Controller
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
    /**
     * @ORM\Column(type="boolean")
     */
    private $tuntap;
    /**
     * @ORM\Column(type="boolean")
     */
    private $fuse;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $os;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $swap;

    // Fetch server status
    public function getStatus($node)
    {
      if ($this->getType() == "openvz") {
        $type = "openvz";
      } else if ($this->getType() == "kvm") {
        $type = "qemu";
      }

      $result = $node->command("get", "/nodes/".$node->getIdentifier()."/".$type."/".$this->getCtid()."/status/current");
      if ($result[0]) {
        $result = $result[1];
        $result["os"] = $this->getOs();
        $result["node"] = $node->getName();
        $result["mem"] = round($result["mem"]/(1024*1024), 0);
        $result["swap"] = round($result["swap"]/(1024*1024), 0);
        $result["disk"] = round($result["disk"]/(1024*1024*1024), 0);
        $result["availablemem"] = $this->getRam();
        $result["availableswap"] = $this->getSwap();
        $result["availabledisk"] = $this->getDisk();
        $result["ram_percent"] = round($result["mem"]*100 / $this->getRam(), 0);
        $result["swap_percent"] = round($result["swap"]*100 / $this->getSwap(), 0);
        $result["disk_percent"] = round($result["disk"]*100 / $this->getDisk(), 0);
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@".$result["uptime"]);
        $result["uptime"] = $dtF->diff($dtT)->format('%a days, %h hours, %i mins');
        return $result;
      } else {
        return false;
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

    /**
     * Set tuntap
     *
     * @param boolean $tuntap
     *
     * @return Server
     */
    public function setTuntap($tuntap)
    {
        $this->tuntap = $tuntap;

        return $this;
    }

    /**
     * Get tuntap
     *
     * @return boolean
     */
    public function getTuntap()
    {
        return $this->tuntap;
    }

    /**
     * Set fuse
     *
     * @param boolean $fuse
     *
     * @return Server
     */
    public function setFuse($fuse)
    {
        $this->fuse = $fuse;

        return $this;
    }

    /**
     * Get fuse
     *
     * @return boolean
     */
    public function getFuse()
    {
        return $this->fuse;
    }

    /**
     * Set os
     *
     * @param string $os
     *
     * @return Server
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set swap
     *
     * @param integer $swap
     *
     * @return Server
     */
    public function setSwap($swap)
    {
        $this->swap = $swap;

        return $this;
    }

    /**
     * Get swap
     *
     * @return integer
     */
    public function getSwap()
    {
        return $this->swap;
    }
}
