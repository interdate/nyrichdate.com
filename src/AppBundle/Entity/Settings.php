<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Settings
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
     * @ORM\Column(name="report_email", type="string", length=255)
     */
    private $reportEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255)
     */
    private $contactEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_from_email", type="string", length=255)
     */
    private $contactFromEmail;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_message_users_number", type="integer", nullable=true)
     */
    private $sendMessageUsersNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="users_per_page", type="integer")
     */
    private $usersPerPage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_charge", type="boolean")
     */
    private $isCharge;

    /**
     * @var integer
     *
     * @ORM\Column(name="message_popularity_days_number", type="integer")
     */
    private $messagePopularityDaysNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_considered_as_online_after_last_activity_minutes_number", type="integer")
     */
    private $userConsideredAsOnlineAfterLastActivityMinutesNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="delete_messages_after_days_number", type="integer", nullable=true)
     */
    private $deleteMessagesAfterDaysNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_considered_as_new_after_days_number", type="integer")
     */
    private $userConsideredAsNewAfterDaysNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_site_paid", type="boolean")
     */
    private $isSitePaid;



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
     * Set reportEmail
     *
     * @param string $reportEmail
     * @return Settings
     */
    public function setReportEmail($reportEmail)
    {
        $this->reportEmail = $reportEmail;

        return $this;
    }

    /**
     * Get reportEmail
     *
     * @return string 
     */
    public function getReportEmail()
    {
        return $this->reportEmail;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return Settings
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string 
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set contactFromEmail
     *
     * @param string $contactFromEmail
     * @return Settings
     */
    public function setContactFromEmail($contactFromEmail)
    {
        $this->contactFromEmail = $contactFromEmail;

        return $this;
    }

    /**
     * Get contactFromEmail
     *
     * @return string
     */
    public function getContactFromEmail()
    {
        return $this->contactFromEmail;
    }

    /**
     * Set sendMessageUsersNumber
     *
     * @param integer $sendMessageUsersNumber
     * @return Settings
     */
    public function setSendMessageUsersNumber($sendMessageUsersNumber)
    {
        $this->sendMessageUsersNumber = $sendMessageUsersNumber;

        return $this;
    }

    /**
     * Get sendMessageUsersNumber
     *
     * @return integer 
     */
    public function getSendMessageUsersNumber()
    {
        return $this->sendMessageUsersNumber;
    }

    /**
     * Set usersPerPage
     *
     * @param integer $usersPerPage
     * @return Settings
     */
    public function setUsersPerPage($usersPerPage)
    {
        $this->usersPerPage = $usersPerPage;

        return $this;
    }

    /**
     * Get usersPerPage
     *
     * @return integer 
     */
    public function getUsersPerPage()
    {
        return $this->usersPerPage;
    }

    /**
     * Set isCharge
     *
     * @param boolean $isCharge
     * @return Settings
     */
    public function setIsCharge($isCharge)
    {
        $this->isCharge = $isCharge;

        return $this;
    }

    /**
     * Get isCharge
     *
     * @return boolean 
     */
    public function getIsCharge()
    {
        return $this->isCharge;
    }

    /**
     * Set messagePopularityDaysNumber
     *
     * @param integer $messagePopularityDaysNumber
     * @return Settings
     */
    public function setMessagePopularityDaysNumber($messagePopularityDaysNumber)
    {
        $this->messagePopularityDaysNumber = $messagePopularityDaysNumber;

        return $this;
    }

    /**
     * Get messagePopularityDaysNumber
     *
     * @return integer 
     */
    public function getMessagePopularityDaysNumber()
    {
        return $this->messagePopularityDaysNumber;
    }

    /**
     * Set userConsideredAsOnlineAfterLastActivityMinutesNumber
     *
     * @param integer $userConsideredAsOnlineAfterLastActivityMinutesNumber
     * @return Settings
     */
    public function setUserConsideredAsOnlineAfterLastActivityMinutesNumber($userConsideredAsOnlineAfterLastActivityMinutesNumber)
    {
        $this->userConsideredAsOnlineAfterLastActivityMinutesNumber = $userConsideredAsOnlineAfterLastActivityMinutesNumber;

        return $this;
    }

    /**
     * Get userConsideredAsOnlineAfterLastActivityMinutesNumber
     *
     * @return integer 
     */
    public function getUserConsideredAsOnlineAfterLastActivityMinutesNumber()
    {
        return $this->userConsideredAsOnlineAfterLastActivityMinutesNumber;
    }

    /**
     * Set deleteMessagesAfterDaysNumber
     *
     * @param integer $deleteMessagesAfterDaysNumber
     * @return Settings
     */
    public function setDeleteMessagesAfterDaysNumber($deleteMessagesAfterDaysNumber)
    {
        $this->deleteMessagesAfterDaysNumber = $deleteMessagesAfterDaysNumber;

        return $this;
    }

    /**
     * Get deleteMessagesAfterDaysNumber
     *
     * @return integer 
     */
    public function getDeleteMessagesAfterDaysNumber()
    {
        return $this->deleteMessagesAfterDaysNumber;
    }

    /**
     * Set userConsideredAsNewAfterDaysNumber
     *
     * @param integer $userConsideredAsNewAfterDaysNumber
     * @return Settings
     */
    public function setUserConsideredAsNewAfterDaysNumber($userConsideredAsNewAfterDaysNumber)
    {
        $this->userConsideredAsNewAfterDaysNumber = $userConsideredAsNewAfterDaysNumber;

        return $this;
    }

    /**
     * Get userConsideredAsNewAfterDaysNumber
     *
     * @return integer 
     */
    public function getUserConsideredAsNewAfterDaysNumber()
    {
        return $this->userConsideredAsNewAfterDaysNumber;
    }

    /**
     * Set isSitePaid
     *
     * @param boolean $isSitePaid
     * @return Settings
     */
    public function setIsSitePaid($isSitePaid)
    {
        $this->isSitePaid = $isSitePaid;

        return $this;
    }

    /**
     * Get isSitePaid
     *
     * @return boolean
     */
    public function getIsSitePaid()
    {
        return $this->isSitePaid;
    }
}
