<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;


/**
 * Area
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AreaRepository")
 */
class Area implements Translatable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="areas")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="ZipCode", mappedBy="area")
     */
    private $zipCodes;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;


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
     * @return Region
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

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }



    /**
     * Set region
     *
     * @param \AppBundle\Entity\Region $region
     *
     * @return Area
     */
    public function setRegion(\AppBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \AppBundle\Entity\Region
     */
    public function getRegion()
    {
        return $this->region;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->zipCodes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add zipCode
     *
     * @param \AppBundle\Entity\ZipCode $zipCode
     *
     * @return Area
     */
    public function addZipCode(\AppBundle\Entity\ZipCode $zipCode)
    {
        $this->zipCodes[] = $zipCode;

        return $this;
    }

    /**
     * Remove zipCode
     *
     * @param \AppBundle\Entity\ZipCode $zipCode
     */
    public function removeZipCode(\AppBundle\Entity\ZipCode $zipCode)
    {
        $this->zipCodes->removeElement($zipCode);
    }

    /**
     * Get zipCodes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getZipCodes()
    {
        return $this->zipCodes;
    }

    public function __toString() {
        return $this->name;
    }
}
