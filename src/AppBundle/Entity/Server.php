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
     * @ORM\Column(type="integer", length=20)
     */
    private $cpu;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $hostname;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"lxc", "kvm"})
     */
    private $type;
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $ctid;
    /**
     * @ORM\Column(type="string", length=20)
     */
    private $nameserver;
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
    private $console;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $os;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $rootpass;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $swap;
    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Choice({"shell", "console", "tty"})
     */
    private $cmode;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $cpulimit;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $cpuunits;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $tty;
    /**
     * @ORM\Column(type="boolean")
     */
    private $unprivileged;
    /**
     * @ORM\Column(type="boolean")
     */
    private $onboot;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $storage;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $search;

    // Fetch server status
    public function getStatus($node, $hash)
    {
      if ($this->getType() == "lxc") {
        $type = "lxc";
      } else if ($this->getType() == "kvm") {
        $type = "qemu";
      }

      $result = $node->command("get", "/nodes/".$node->getIdentifier()."/".$type."/".$this->getCtid()."/status/current", $hash);
      if ($result[0] && $result[1]) {
        $result = $result[1];
        $result["os"] = $this->getOs();
        $result["tuntap"] = $this->getTuntap();
        $result["node"] = $node->getName();
        $result["mem"] = round($result["mem"]/(1024*1024), 0);
        $result["swap"] = round($result["swap"]/(1024*1024), 0);
        $result["disk"] = round($result["disk"]/(1024*1024*1024), 1);
        $result["cpu"] = round($result["cpu"]*100, 1);
        $result["availablemem"] = $this->getRam();
        $result["availableswap"] = $this->getSwap();
        $result["availabledisk"] = $this->getDisk();
        $result["ram_percent"] = round($result["mem"]*100 / $this->getRam(), 0);
        $result["swap_percent"] = round($result["swap"]*100 / $this->getSwap(), 0);
        $result["disk_percent"] = round($result["disk"]*100 / $this->getDisk(), 0);
        $result["ip"] = $this->getIp();
        $result["nameserver"] = $this->getNameserver();
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@".$result["uptime"]);
        $result["uptime"] = $dtF->diff($dtT)->format('%a days, %h hours, %i mins');
        return $result;
      } else {
        return false;
      }

    }

    // Fetch graph
    public function getGraph($node, $type, $period, $hash)
    {
      if ($this->getType() == "lxc") {
        $vmtype = "lxc";
      } else if ($this->getType() == "kvm") {
        $vmtype = "qemu";
      }
      $result = $node->command("get", "/nodes/".$node->getIdentifier()."/".$vmtype."/".$this->getCtid()."/rrd", $hash, ['ds' => $type, 'timeframe' => $period], true);
      return $result;
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

    /**
     * Set nameserver
     *
     * @param string $nameserver
     *
     * @return Server
     */
    public function setNameserver($nameserver)
    {
        $this->nameserver = $nameserver;

        return $this;
    }

    /**
     * Get nameserver
     *
     * @return string
     */
    public function getNameserver()
    {
        return $this->nameserver;
    }

    /**
     * Set rootpass
     *
     * @param string $rootpass
     *
     * @return Server
     */
    public function setRootpass($rootpass)
    {
        $this->rootpass = $rootpass;

        return $this;
    }

    /**
     * Get rootpass
     *
     * @return string
     */
    public function getRootpass()
    {
        return $this->rootpass;
    }

    /**
     * Set console
     *
     * @param boolean $console
     *
     * @return Server
     */
    public function setConsole($console)
    {
        $this->console = $console;

        return $this;
    }

    /**
     * Get console
     *
     * @return boolean
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * Set cmode
     *
     * @param string $cmode
     *
     * @return Server
     */
    public function setCmode($cmode)
    {
        $this->cmode = $cmode;

        return $this;
    }

    /**
     * Get cmode
     *
     * @return string
     */
    public function getCmode()
    {
        return $this->cmode;
    }

    /**
     * Set cpulimit
     *
     * @param integer $cpulimit
     *
     * @return Server
     */
    public function setCpulimit($cpulimit)
    {
        $this->cpulimit = $cpulimit;

        return $this;
    }

    /**
     * Get cpulimit
     *
     * @return integer
     */
    public function getCpulimit()
    {
        return $this->cpulimit;
    }

    /**
     * Set cpuunits
     *
     * @param integer $cpuunits
     *
     * @return Server
     */
    public function setCpuunits($cpuunits)
    {
        $this->cpuunits = $cpuunits;

        return $this;
    }

    /**
     * Get cpuunits
     *
     * @return integer
     */
    public function getCpuunits()
    {
        return $this->cpuunits;
    }

    /**
     * Set tty
     *
     * @param integer $tty
     *
     * @return Server
     */
    public function setTty($tty)
    {
        $this->tty = $tty;

        return $this;
    }

    /**
     * Get tty
     *
     * @return integer
     */
    public function getTty()
    {
        return $this->tty;
    }

    /**
     * Set unpriviliged
     *
     * @param boolean $unpriviliged
     *
     * @return Server
     */
    public function setUnpriviliged($unpriviliged)
    {
        $this->unpriviliged = $unpriviliged;

        return $this;
    }

    /**
     * Get unpriviliged
     *
     * @return boolean
     */
    public function getUnpriviliged()
    {
        return $this->unpriviliged;
    }

    /**
     * Set unprivileged
     *
     * @param boolean $unprivileged
     *
     * @return Server
     */
    public function setUnprivileged($unprivileged)
    {
        $this->unprivileged = $unprivileged;

        return $this;
    }

    /**
     * Get unprivileged
     *
     * @return boolean
     */
    public function getUnprivileged()
    {
        return $this->unprivileged;
    }

    /**
     * Set cpu
     *
     * @param integer $cpu
     *
     * @return Server
     */
    public function setCpu($cpu)
    {
        $this->cpu = $cpu;

        return $this;
    }

    /**
     * Get cpu
     *
     * @return integer
     */
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * Set onboot
     *
     * @param boolean $onboot
     *
     * @return Server
     */
    public function setOnboot($onboot)
    {
        $this->onboot = $onboot;

        return $this;
    }

    /**
     * Get onboot
     *
     * @return boolean
     */
    public function getOnboot()
    {
        return $this->onboot;
    }

    /**
     * Set storage
     *
     * @param string $storage
     *
     * @return Server
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get storage
     *
     * @return string
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set search
     *
     * @param string $search
     *
     * @return Server
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get search
     *
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }
}
