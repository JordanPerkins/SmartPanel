<?php
/* Node entity created by Jordan Perkins
 * This is used to store info regarding nodes.
 * It handles command issuing to the backend.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


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
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    // Function for sending commands to the node.
    public function command($cmd, $type, $data) {

        $postfields = array();
        $postfields["user"] = $this->getUsername();
        $postfields["pass"] = $this->getPassword();
        $postfields["cmd"] = $cmd;
        $postfields["type"] = $type;
        $postfields = array_merge($postfields, $data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://".$this->getIp().":8471/command.php");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        if (curl_error($ch)) {
          return curl_errno($ch);
        }
        curl_close($ch);
        $response = json_decode($response, true);
        return $response;

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
}
