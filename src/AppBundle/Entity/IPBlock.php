<?php
/* Template entity created by Jordan Perkins
 * This is used to store info regarding templates.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="app_ipblock")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IPBlockRepository")
 */
class IPBlock
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
    private $gateway;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $netmask;


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
     * Set gateway
     *
     * @param string $gateway
     *
     * @return IPBlock
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * Get gateway
     *
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * Set netmask
     *
     * @param string $netmask
     *
     * @return IPBlock
     */
    public function setNetmask($netmask)
    {
        $this->netmask = $netmask;

        return $this;
    }

    /**
     * Get netmask
     *
     * @return string
     */
    public function getNetmask()
    {
        return $this->netmask;
    }
}
