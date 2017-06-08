<?php
/* Node entity created by Jordan Perkins
 * This is used to store info regarding nodes.
 * It handles command issuing to the backend.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ProxmoxVE\Proxmox;
use Symfony\Component\Config\Definition\Exception\Exception;


/**
 * @ORM\Table(name="app_nodes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NodeRepository")
 */
class Node
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
    private $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $identifier;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $realm;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $username;

    /**
     * @ORM\Column(type="integer", length=64)
     */
    private $port;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    // Function for sending commands to the node.
    public function command($type, $cmd, $data = null, $image = false) {
      $credentials = [
        'hostname' => $this->getIp(),
        'username' => $this->getUsername(),
        'password' => $this->getPassword(),
        'realm' => $this->getRealm(),
        'port' => $this->getPort(),
        ];
        $proxmox = new Proxmox($credentials);

        if ($type == "get" || $type == "create" || $type == "set" || $type == "delete") {
          if ($data == null) {
            $result = $proxmox->$type($cmd);
          } else {
            if ($image == true) {
              $proxmox->setResponseType('png');
            }
            $result = $proxmox->$type($cmd, $data);
          }
          if (isset($result['errors'])) {
            return [false, null];
          } else {
            if ($image == true) {
              return $result;
            } else {
              return [true, $result['data']];
            }
          }
        } else {
          return [false, null];
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
     * Set ip
     *
     * @param string $ip
     *
     * @return Node
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
     * Set name
     *
     * @param string $name
     *
     * @return Node
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
     * Set username
     *
     * @param string $username
     *
     * @return Node
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Node
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set realm
     *
     * @param string $realm
     *
     * @return Node
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Get realm
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Set port
     *
     * @param integer $port
     *
     * @return Node
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return Node
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
