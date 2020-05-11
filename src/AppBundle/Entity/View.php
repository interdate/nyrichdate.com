<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * View
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ViewRepository")
 */
class View
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="viewed")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     **/
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="viewedMe")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     **/
    private $member;


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
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     * @return View
     */
    public function setOwner(\AppBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set member
     *
     * @param \AppBundle\Entity\User $member
     * @return View
     */
    public function setMember(\AppBundle\Entity\User $member = null)
    {
        $this->member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \AppBundle\Entity\User 
     */
    public function getMember()
    {
        return $this->member;
    }
}
