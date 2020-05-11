<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Video extends File
{
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="videos")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    public function getUploadDir()
    {
        return '/media/videos/' . $this->user->getId();
    }

    public function getNoFile()
    {
        return $this->user->getNoVideo();
    }
}
