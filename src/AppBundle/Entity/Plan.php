<?php
/* Template entity created by Jordan Perkins
 * This is used to store info regarding templates.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="app_plan")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlanRepository")
 */
class Plan
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"lxc", "kvm"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $name;

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
    private $swap;

    /**
     * @ORM\Column(type="boolean")
     */
    private $console;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Choice({"shell", "console", "tty"})
     */
    private $cmode;

    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $cpu;

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
     * @ORM\Column(type="integer", length=20)
     */
    private $bandwidth;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $nameserver;

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
    private $searchdomain;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $ipv4;
    /**
     * @ORM\Column(type="integer", length=20)
     */
    private $ipv6;


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
     * Set type
     *
     * @param string $type
     *
     * @return Plan
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
     * Set name
     *
     * @param string $name
     *
     * @return Plan
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set disk
     *
     * @param integer $disk
     *
     * @return Plan
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
     * @return Plan
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
     * Set swap
     *
     * @param integer $swap
     *
     * @return Plan
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
     * Set console
     *
     * @param boolean $console
     *
     * @return Plan
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
     * @return Plan
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
     * Set cpu
     *
     * @param integer $cpu
     *
     * @return Plan
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
     * Set cpulimit
     *
     * @param integer $cpulimit
     *
     * @return Plan
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
     * @return Plan
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
     * @return Plan
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
     * Set unprivileged
     *
     * @param boolean $unprivileged
     *
     * @return Plan
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
     * Set onboot
     *
     * @param boolean $onboot
     *
     * @return Plan
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
     * @return Plan
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
     * Set searchdomain
     *
     * @param string $searchdomain
     *
     * @return Plan
     */
    public function setSearchdomain($searchdomain)
    {
        $this->searchdomain = $searchdomain;

        return $this;
    }

    /**
     * Get searchdomain
     *
     * @return string
     */
    public function getSearchdomain()
    {
        return $this->searchdomain;
    }

    /**
     * Set ipv4
     *
     * @param integer $ipv4
     *
     * @return Plan
     */
    public function setIpv4($ipv4)
    {
        $this->ipv4 = $ipv4;

        return $this;
    }

    /**
     * Get ipv4
     *
     * @return integer
     */
    public function getIpv4()
    {
        return $this->ipv4;
    }

    /**
     * Set ipv6
     *
     * @param integer $ipv6
     *
     * @return Plan
     */
    public function setIpv6($ipv6)
    {
        $this->ipv6 = $ipv6;

        return $this;
    }

    /**
     * Get ipv6
     *
     * @return integer
     */
    public function getIpv6()
    {
        return $this->ipv6;
    }

    /**
     * Set bandwidth
     *
     * @param integer $bandwidth
     *
     * @return Plan
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
     * Set nameserver
     *
     * @param string $nameserver
     *
     * @return Plan
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
}
