<?php
/* Settings entity created by Jordan Perkins
 * This is used to store global panel settings.
*/
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="app_settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
    private $setting;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $numerical;


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
     * Set setting
     *
     * @param string $setting
     *
     * @return Settings
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Get setting
     *
     * @return string
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Settings
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
     * Set numerical
     *
     * @param boolean $numerical
     *
     * @return Settings
     */
    public function setNumerical($numerical)
    {
        $this->numerical = $numerical;

        return $this;
    }

    /**
     * Get numerical
     *
     * @return boolean
     */
    public function getNumerical()
    {
        return $this->numerical;
    }
}
