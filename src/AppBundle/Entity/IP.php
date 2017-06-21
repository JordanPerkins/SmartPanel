<?php
/* Template entity created by Jordan Perkins
 * This is used to store info regarding templates.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="app_ip")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IPRepository")
 */
class IP
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
    private $ip;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $interface;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $rdns;

    /**
     * @ORM\Column(type="integer", length=1)
     */
    private $version;

    /**
     * @ORM\Column(type="integer", length=64)
     */
    private $block;

    /**
     * @ORM\Column(type="integer", length=64)
     */
    private $sid;




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
     * Set ip
     *
     * @param string $ip
     *
     * @return IP
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
     * Set version
     *
     * @param integer $version
     *
     * @return IP
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set block
     *
     * @param integer $block
     *
     * @return IP
     */
    public function setBlock($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return integer
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set sid
     *
     * @param integer $sid
     *
     * @return IP
     */
    public function setSid($sid)
    {
        $this->sid = $sid;

        return $this;
    }

    /**
     * Get sid
     *
     * @return integer
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set interface
     *
     * @param string $interface
     *
     * @return IP
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;

        return $this;
    }

    /**
     * Get interface
     *
     * @return string
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * Set rdns
     *
     * @param string $rdns
     *
     * @return IP
     */
    public function setRdns($rdns)
    {
        $this->rdns = $rdns;

        return $this;
    }

    /**
     * Get rdns
     *
     * @return string
     */
    public function getRdns()
    {
        return $this->rdns;
    }
}
