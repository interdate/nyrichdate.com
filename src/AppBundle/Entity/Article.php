<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Translatable;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Article implements Translatable
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
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="brief", type="text")
     */
    private $brief;

    /**
     * @var string
     *
     * @ORM\Column(name="header_type", type="string", length=2)
     */
    private $headerType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_on_homepage", type="boolean")
     */
    private $isOnHomepage;

    /**
     * @var date
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="uri", type="string", length=255)
     */
    private $uri;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="image_alt", type="string", length=255, nullable=true)
     */
    private $imageAlt;

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    protected $ext;

    protected $file;


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
     * @return Article
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
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Article
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set brief
     *
     * @param string $brief
     * @return Article
     */
    public function setBrief($brief)
    {
        $this->brief = $brief;

        return $this;
    }

    /**
     * Get brief
     *
     * @return string 
     */
    public function getBrief()
    {
        return $this->brief;
    }

    /**
     * Set headerType
     *
     * @param string $headerType
     * @return Article
     */
    public function setHeaderType($headerType)
    {
        $this->headerType = $headerType;

        return $this;
    }

    /**
     * Get headerType
     *
     * @return string
     */
    public function getHeaderType()
    {
        return $this->headerType;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Article
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isOnHomepage
     *
     * @param boolean $isOnHomepage
     * @return Article
     */
    public function setIsOnHomepage($isOnHomepage)
    {
        $this->isOnHomepage = $isOnHomepage;

        return $this;
    }

    /**
     * Get isOnHomepage
     *
     * @return boolean
     */
    public function getIsOnHomepage()
    {
        return $this->isOnHomepage;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Article
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * Set uri
     *
     * @param string $uri
     * @return Article
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Get uri
     *
     * @return string 
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set imageAlt
     *
     * @param string $imageAlt
     * @return Article
     */
    public function setImageAlt($imageAlt)
    {
        $this->imageAlt = $imageAlt;

        return $this;
    }

    /**
     * Get imageAlt
     *
     * @return string
     */
    public function getImageAlt()
    {
        return $this->imageAlt;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Set ext
     *
     * @param string $ext
     * @return File
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return integer
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function preUpload()
    {
        if (null !== $this->file) {

            if(is_file($this->getFileAbsolutePath())){
                unlink($this->getFileAbsolutePath());
            }

            $this->ext = $this->file->guessExtension();
        }
    }

    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->file->move(
            $this->getUploadRootDir(),
            $this->id . '.' .$this->ext
        );

        // set the ext property to the filename where you've saved the file
        //$this->ext = $this->file->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    public function getUploadRootDir()
    {
        return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir();
    }

    public function getUploadDir()
    {
        return '/media/articles/';
    }

    public function getFileWebPath()
    {
        return $this->getUploadDir() . $this->id . '.' . $this->ext;
    }

    public function getFileAbsolutePath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir() . $this->id . '.' . $this->ext;
    }
}
