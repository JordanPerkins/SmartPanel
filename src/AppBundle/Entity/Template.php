<?php
/* Template entity created by Jordan Perkins
 * This is used to store info regarding templates.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="app_templates")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TemplateRepository")
 */
class Template
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
    private $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"openvz", "kvm"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"amd64", "i386"})
     */
    private $arch;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"tar.gz", "tar.xz"})
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\Choice({"local"})
     */
    private $storage;



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
     * Set name
     *
     * @param string $name
     *
     * @return Template
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
     * Set file
     *
     * @param string $file
     *
     * @return Template
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Template
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
     * Set arch
     *
     * @param string $arch
     *
     * @return Template
     */
    public function setArch($arch)
    {
        $this->arch = $arch;

        return $this;
    }

    /**
     * Get arch
     *
     * @return string
     */
    public function getArch()
    {
        return $this->arch;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Template
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set storage
     *
     * @param string $storage
     *
     * @return Template
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
}
