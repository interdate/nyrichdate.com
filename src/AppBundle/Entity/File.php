<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File as UploadedFile;
use JMS\Serializer\Annotation\Discriminator;


/**
 * Class File
 *
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="integer", length=1)
 * @ORM\DiscriminatorMap({"1" = "Photo", "2" = "Video"})
 * @ORM\HasLifecycleCallbacks()
 * @Discriminator(disabled=true)
 */

abstract class File
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=7)
     */
    protected $ext;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean")
     */
    protected $isValid;

    /**
     * File type
     * @var string
     */
    protected $type;

    
    protected $file;


    protected $user;


    protected $deletedId = null;


    /*public function __construct($type){
        $this->type = $type;        
    }*/


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
     * Set user
     *
     * @param integer $user
     * @return Photo
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
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
     * Set isValid
     *
     * @param boolean $isValid
     * @return File
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->isValid;
    }


    /**
     * Called before saving the entity
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            if(!is_dir($this->getUploadRootDir())){
                mkdir($this->getUploadRootDir(), 0777, true);
            }

            $this->ext = $this->file->guessExtension();
        }
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->deletedId = $this->id;
    }

    /**
     * Called before entity removal
     *
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if (null !== $file && is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
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

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }


    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        if(null !== $this->deletedId){
            $this->id = $this->deletedId;
        }

        return null === $this->ext
            ? null
            : $this->getUploadRootDir() . '/' . $this->id . '.' . $this->ext;
    }

    public function getWebPath()
    {
        $webPath = $this->getUploadDir() . '/' . $this->id . '.' . $this->ext;
        return is_file($_SERVER['DOCUMENT_ROOT'] . $webPath) ? $webPath : $this->getNoFile();
    }

    public function getUploadRootDir()
    {
        return $_SERVER['DOCUMENT_ROOT'] . $this->getUploadDir();
    }

    abstract function getUploadDir();

    abstract function getNoFile();

}
