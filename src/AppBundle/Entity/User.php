<?php

namespace AppBundle\Entity;

use AppBundle\AppBundle;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\Criteria;
/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
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
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     **/
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=20)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="ZipCode", inversedBy="users")
     * @ORM\JoinColumn(name="zip_code_id", referencedColumnName="id")
     */
    private $zipCode;

    /**
     * @ORM\ManyToOne(targetEntity="Gender", inversedBy="users")
     * @ORM\JoinColumn(name="gender_id", referencedColumnName="id")
     */
    private $gender;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * @ORM\ManyToOne(targetEntity="RelationshipStatus", inversedBy="users")
     * @ORM\JoinColumn(name="relationship_status_id", referencedColumnName="id")
     */
    private $relationshipStatus;

    /**
     * @ORM\ManyToOne(targetEntity="Education", inversedBy="users")
     * @ORM\JoinColumn(name="education_id", referencedColumnName="id")
     */
    private $education;

    /**
     * @ORM\ManyToOne(targetEntity="Religion", inversedBy="users")
     * @ORM\JoinColumn(name="religion_id", referencedColumnName="id")
     */
    private $religion;

    /**
     * @ORM\ManyToMany(targetEntity="Purpose", inversedBy="users")
     * @ORM\JoinTable(name="users_purposes")
     **/
    private $purposes;

    /**
     * @ORM\ManyToMany(targetEntity="Language", inversedBy="users")
     * @ORM\JoinTable(name="users_languages")
     **/
    private $languages;

    /**
     * @ORM\ManyToMany(targetEntity="Feature", inversedBy="users")
     * @ORM\JoinTable(name="users_features")
     **/
    private $features;

    /**
     * @ORM\ManyToMany(targetEntity="Hobby", inversedBy="users")
     * @ORM\JoinTable(name="users_hobbies")
     **/
    private $hobbies;

    /**
     * @ORM\ManyToMany(targetEntity="PaymentHistory", inversedBy="users")
     * @ORM\JoinTable(name="users_payment_History")
     **/
    private $paymentHistory;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=255, nullable=true)
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text")
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="looking", type="text")
     */
    private $looking;

    /**
     * @ORM\ManyToOne(targetEntity="Smoking", inversedBy="users")
     * @ORM\JoinColumn(name="smoking_id", referencedColumnName="id")
     */
    private $smoking;

    /**
     * @ORM\ManyToOne(targetEntity="Drinking", inversedBy="users")
     * @ORM\JoinColumn(name="drinking_id", referencedColumnName="id")
     */
    private $drinking;

    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="users")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="NetWorth", inversedBy="users")
     * @ORM\JoinColumn(name="net_worth_id", referencedColumnName="id", nullable=true)
     */
    private $netWorth;

    /**
     * @ORM\ManyToOne(targetEntity="Income", inversedBy="users")
     * @ORM\JoinColumn(name="income_id", referencedColumnName="id", nullable=true)
     */
    private $income;

    /**
     * @ORM\ManyToOne(targetEntity="Children", inversedBy="users")
     * @ORM\JoinColumn(name="children_id", referencedColumnName="id")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Zodiac", inversedBy="users")
     * @ORM\JoinColumn(name="zodiac_id", referencedColumnName="id")
     */
    private $zodiac;
    
    /**
     * @ORM\ManyToOne(targetEntity="Ethnicity", inversedBy="users")
     * @ORM\JoinColumn(name="ethnicity_id", referencedColumnName="id")
     */
    private $ethnicity;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float")
     */
    private $height;

    /**
     * @ORM\ManyToOne(targetEntity="Body", inversedBy="users")
     * @ORM\JoinColumn(name="body_id", referencedColumnName="id")
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="Eyes", inversedBy="users")
     * @ORM\JoinColumn(name="eyes_id", referencedColumnName="id")
     */
    private $eyes;

    /**
     * @ORM\ManyToOne(targetEntity="Hair", inversedBy="users")
     * @ORM\JoinColumn(name="hair_id", referencedColumnName="id")
     */
    private $hair;

    /**
     * @ORM\ManyToOne(targetEntity="LoginFrom", inversedBy="users")
     * @ORM\JoinColumn(name="login_from_id", referencedColumnName="id")
     */
    private $loginFrom;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="Video", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity="View", mappedBy="owner", cascade={"remove"}, orphanRemoval=true)
     */
    private $viewed;

    /**
     * @ORM\OneToMany(targetEntity="View", mappedBy="member", cascade={"remove"}, orphanRemoval=true)
     */
    private $viewedMe;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="owner", cascade={"remove"}, orphanRemoval=true)
     */
    private $contacted;

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="member", cascade={"remove"}, orphanRemoval=true)
     */
    private $contactedMe;

    /**
     * @ORM\OneToMany(targetEntity="Favorite", mappedBy="owner", cascade={"remove"}, orphanRemoval=true)
     */
    private $favorited;

    /**
     * @ORM\OneToMany(targetEntity="Favorite", mappedBy="member", cascade={"remove"}, orphanRemoval=true)
     */
    private $favoritedMe;

    /**
     * @ORM\OneToMany(targetEntity="BlackList", mappedBy="owner", cascade={"remove"}, orphanRemoval=true)
     */
    private $blackListed;

    /**
     * @ORM\OneToMany(targetEntity="BlackList", mappedBy="member", cascade={"remove"}, orphanRemoval=true)
     */
    private $blackListedMe;

    /**
     * @ORM\OneToMany(targetEntity="Communication", mappedBy="owner", cascade={"remove"}, orphanRemoval=true)
     */
    private $connected;

    /**
     * @ORM\OneToMany(targetEntity="Communication", mappedBy="member", cascade={"remove"}, orphanRemoval=true)
     */
    private $connectedMe;

    /**
     * @ORM\OneToMany(targetEntity="UserNotifications", mappedBy="user", cascade={"remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"isRead" = "ASC", "date" = "DESC"})
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity="LikeMe", mappedBy="userFrom", cascade={"remove"}, orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="LikeMe", mappedBy="userTo", cascade={"remove"}, orphanRemoval=true)
     */
    private $likesMe;



    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer")
     */
    private $points = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="freeze_reason", type="string", length=255, nullable=true)
     */
    private $freezeReason;

    /**
     * @var string
     *
     * @ORM\Column(name="ban_reason", type="string", length=255, nullable=true)
     */
    private $banReason;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_comments", type="string", length=255, nullable=true)
     */
    private $adminComments;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15, nullable=true)
     */
    private $ip;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_activated", type="boolean", nullable=true)
     */
    private $isActivated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_non_locked", type="boolean")
     */
    private $isNonLocked = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_frozen", type="boolean")
     */
    private $isFrozen = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_flagged", type="boolean")
     */
    private $isFlagged = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_on_homepage", type="boolean")
     */
    private $isOnHomepage = false;

    /**
     * Date/Time of the last activity
     *
     * @var \Datetime
     * @ORM\Column(name="last_activity_at", type="datetime")
     */
    protected $lastActivityAt;

    /**
     * Date/Time of the last real activity
     *
     * @var \Datetime
     * @ORM\Column(name="last_real_activity_at", type="datetime", nullable=true)
     */
    protected $lastRealActivityAt;

    /**
     * Date/Time of the last login
     *
     * @var \Datetime
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     */
    protected $lastloginAt;

    /**
     * Date/Time of the signing up
     *
     * @var \Datetime
     * @ORM\Column(name="sign_up_date", type="datetime")
     */
    protected $signUpDate;

    /**
     * @var \Datetime
     * @ORM\Column(name="start_subscription", type="datetime", nullable=true)
     */
    protected $startSubscription;

    /**
     * @var \Datetime
     * @ORM\Column(name="end_subscription", type="datetime", nullable=true)
     */
    protected $endSubscription;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=20, nullable=true)
     */
    protected $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sent_email", type="boolean")
     */
    protected $isSentEmail = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_sent_push", type="boolean")
     */
    protected $isSentPush = true;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="integer", length=5, nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="sms_count", type="integer", length=2, nullable=true)
     */
    protected $smsCount = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     */
    private $latitude;

    /**
     * @var string
     */
    private $oldPassword;

    private $region;

    private $area;


    public function setRegion($region = null)
    {
        $this->region = $region;

        return $this;
    }

    public function getRegion()
    {
        return null !== $this->zipCode ? $this->zipCode->getArea()->getRegion() : $this->region;
        //return $this->region;
    }

    public function setArea($area = null)
    {
        $this->area = $area;

        return $this;
    }


    public function getArea()
    {
        return null !== $this->zipCode ? $this->zipCode->getArea() : $this->area;
        //return $this->area;
    }




    public function __construct() {
        $this->purposes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->features = new \Doctrine\Common\Collections\ArrayCollection();
        $this->hobbies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paymentHistory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->likes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->likesMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->salt = null;
    }




    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    public function getRoles()
    {
        return array($this->role->getRole());
    }

    public function getRoleSystemName()
    {
        return $this->role->getRole();
    }

    public function isAdmin()
    {
        return $this->role->getRole() == 'ROLE_ADMIN';
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }


    public function __toString()
    {
        return strval($this->id);
    }


    public function isEnabled()
    {
        return $this->isActive;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    public function getIsActivated()
    {
        return $this->isActivated;
    }

    public function isAccountNonLocked()
    {
        return $this->isNonLocked;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isGranted($role)
    {
        return in_array($role, $this->getRoles());
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function age()
    {
        return date_diff(date_create($this->birthday->format('Y-m-d')), date_create('today'))->y;
    }

    public function isPaying()
    {
        $format = 'Y-m-d H:i:s';
        $date = date($format);
        $dateNow = new \DateTime($date);
        $interval = new \DateInterval('PT3H'); // add 3 hover for time renew
        $startDate = $this->getStartSubscription();
        $endDate = $this->getEndSubscription();
        $startDate = ($startDate instanceof \DateTime) ? $startDate->sub($interval) : $startDate;
        $endDate = ($endDate instanceof \DateTime) ? $endDate->add($interval) : $endDate;
        return
            $startDate instanceof \DateTime
            && $endDate instanceof \DateTime
            && $startDate <= $dateNow
            && $endDate >= $dateNow
            ;
    }


    /**
     * @return Bool Whether the user is 2 day or not
     */
    public function is2D($days = 2)
    {
        $days = ((int)$days > 0) ? (int)$days : 2;
        $interval = new \DateInterval('P'.$days.'D');
        $dateNow = new \DateTime(date('Y-m-d H:i:s'));
        $signUp2D = $this->getSignUpDate()->add($interval);
        return ($signUp2D >= $dateNow);
    }


    public function apiKey()
    {
        return md5($this->id) . md5($this->password);
    }


    public function getNoPhoto()
    {
        return '/images/no_photo_' . $this->gender->getId() . '.png';
    }

    public function getNoVideo()
    {
        return '/images/no_video.jpg';
    }

    public function hasPhotos()
    {
        return count($this->photos) > 0;
    }

    public function hasValidPhotos()
    {
        foreach($this->photos as $photo){
            if($photo->getIsValid()){
                return true;
            }
        }

        return false;
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
     * Set username
     *
     * @param string $username
     * @return User
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
     * @return User
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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set gender
     *
     * @param integer $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set relationshipStatus
     *
     * @param integer relationshipStatus
     * @return User
     */
    public function setRelationshipStatus($relationshipStatus)
    {
        $this->relationshipStatus = $relationshipStatus;

        return $this;
    }

    /**
     * Get relationshipStatus
     *
     * @return integer 
     */
    public function getRelationshipStatus()
    {
        return $this->relationshipStatus;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return User
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;

        return $this;
    }

    /**
     * Get occupation
     *
     * @return string
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set education
     *
     * @param integer $education
     * @return User
     */
    public function setEducation($education)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return integer 
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set religion
     *
     * @param integer $religion
     * @return User
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;

        return $this;
    }

    /**
     * Get religion
     *
     * @return integer 
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set looking
     *
     * @param string $looking
     * @return User
     */
    public function setLooking($looking)
    {
        $this->looking = $looking;

        return $this;
    }

    /**
     * Get looking
     *
     * @return string 
     */
    public function getLooking()
    {
        return $this->looking;
    }

    /**
     * Set smoking
     *
     * @param integer $smoking
     * @return User
     */
    public function setSmoking($smoking)
    {
        $this->smoking = $smoking;

        return $this;
    }

    /**
     * Get smoking
     *
     * @return integer 
     */
    public function getSmoking()
    {
        return $this->smoking;
    }

    /**
     * Set drinking
     *
     * @param integer $drinking
     * @return User
     */
    public function setDrinking($drinking)
    {
        $this->drinking = $drinking;

        return $this;
    }

    /**
     * Get drinking
     *
     * @return integer 
     */
    public function getDrinking()
    {
        return $this->drinking;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set netWorth
     *
     * @param integer $netWorth
     * @return User
     */
    public function setNetWorth($netWorth)
    {
        $this->netWorth = $netWorth;

        return $this;
    }

    /**
     * Get netWorth
     *
     * @return integer
     */
    public function getNetWorth()
    {
        return $this->netWorth;
    }

    /**
     * Set income
     *
     * @param integer $income
     * @return User
     */
    public function setIncome($income)
    {
        $this->income = $income;

        return $this;
    }

    /**
     * Get income
     *
     * @return integer
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set children
     *
     * @param integer $children
     * @return User
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return integer 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set height
     *
     * @param float $height
     * @return User
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return float 
     */
    public function getHeight($hValue = false)
    {
        if (!$hValue) {
            return $this->height;
        }
        $heightValue = '';
        if (!empty($this->height)) {
            for ($i = 54; $i <= 96; $i++) {
                if ($this->height == round($i * 2.54)) {
                    $heightValue = (int)($i / 12) . "' " . ($i % 12) . "\" (" . round($i * 2.54) . " cm)";
                    break;
                }
            }
        }
        return $heightValue;
    }

    /**
     * Set body
     *
     * @param integer $body
     * @return User
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return integer 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set eyes
     *
     * @param integer $eyes
     * @return User
     */
    public function setEyes($eyes)
    {
        $this->eyes = $eyes;

        return $this;
    }

    /**
     * Get eyes
     *
     * @return integer 
     */
    public function getEyes()
    {
        return $this->eyes;
    }

    /**
     * Set hair
     *
     * @param integer $hair
     * @return User
     */
    public function setHair($hair)
    {
        $this->hair = $hair;

        return $this;
    }

    /**
     * Get hair
     *
     * @return integer 
     */
    public function getHair()
    {
        return $this->hair;
    }

    /**
     * Add purposes
     *
     * @param \AppBundle\Entity\Purpose $purposes
     * @return User
     */
    public function addPurpose(\AppBundle\Entity\Purpose $purposes)
    {
        $this->purposes[] = $purposes;

        return $this;
    }

    /**
     * Remove purposes
     *
     * @param \AppBundle\Entity\Purpose $purposes
     */
    public function removePurpose(\AppBundle\Entity\Purpose $purposes)
    {
        $this->purposes->removeElement($purposes);
    }

    /**
     * Get purposes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPurposes()
    {
        return $this->purposes;
    }

    /**
     * Add languages
     *
     * @param \AppBundle\Entity\Language $languages
     * @return User
     */
    public function addLanguage(\AppBundle\Entity\Language $languages)
    {
        $this->languages[] = $languages;

        return $this;
    }

    /**
     * Remove languages
     *
     * @param \AppBundle\Entity\Language $languages
     */
    public function removeLanguage(\AppBundle\Entity\Language $languages)
    {
        $this->languages->removeElement($languages);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Add paymentHistory
     *
     * @param \AppBundle\Entity\PaymentHistory $paymentHistory
     * @return User
     */
    public function addPaymentHistory(\AppBundle\Entity\PaymentHistory $paymentHistory)
    {
        $this->paymentHistory[] = $paymentHistory;

        return $this;
    }

    /**
     * Remove paymentHistory
     *
     * @param \AppBundle\Entity\PaymentHistory $paymentHistory
     */
    public function removePaymentHistory(\AppBundle\Entity\PaymentHistory $paymentHistory)
    {
        $this->paymentHistory->removeElement($paymentHistory);
    }

    /**
     * Get languages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentHistory()
    {
        return $this->paymentHistory;
    }

    /**
     * Add features
     *
     * @param \AppBundle\Entity\Feature $features
     * @return User
     */
    public function addFeature(\AppBundle\Entity\Feature $features)
    {
        $this->features[] = $features;

        return $this;
    }

    /**
     * Remove features
     *
     * @param \AppBundle\Entity\Feature $features
     */
    public function removeFeature(\AppBundle\Entity\Feature $features)
    {
        $this->features->removeElement($features);
    }

    /**
     * Get features
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * Add photos
     *
     * @param \AppBundle\Entity\Photo $photos
     * @return User
     */
    public function addPhoto(\AppBundle\Entity\Photo $photos)
    {
        $this->photos[] = $photos;

        return $this;
    }

    /**
     * Remove photos
     *
     * @param \AppBundle\Entity\Photo $photos
     */
    public function removePhoto(\AppBundle\Entity\Photo $photos)
    {
        $this->photos->removeElement($photos);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Get main photo
     *
     * @return \AppBundle\Entity\Photo
     */
    public function getMainPhoto()
    {
        foreach($this->photos as $photo){
            if($photo->getIsMain() && $photo->getIsValid()){
                return $photo;
            }
        }

        return null;


        //It's the same just through the query to DB
        /*
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("isValid", true))
            ->andWhere(Criteria::expr()->eq("isMain", true))
        ;
        $collection = $this->getPhotos()->matching($criteria);
        return $collection[0];
        */
    }

    public function isMainPhoto(){
        foreach($this->photos as $photo){
            if($photo->getIsMain()){
                return true;
            }
        }
        return false;
    }


    /**
     * Add videos
     *
     * @param \AppBundle\Entity\Video $videos
     * @return User
     */
    public function addVideo(\AppBundle\Entity\Video $videos)
    {
        $this->videos[] = $videos;

        return $this;
    }

    /**
     * Remove videos
     *
     * @param \AppBundle\Entity\Video $videos
     */
    public function removeVideo(\AppBundle\Entity\Video $videos)
    {
        $this->videos->removeElement($videos);
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * @param \Datetime $lastActivityAt
     */
    public function setLastActivityAt($lastActivityAt)
    {
        $this->lastActivityAt = $lastActivityAt;
    }

    /**
     * @return \Datetime
     */
    public function getLastActivityAt()
    {
        return $this->lastActivityAt;
    }

    /**
     * @return Bool Whether the user is active or not
     */
    public function isOnline($minutes = 1440)
    {
        // Delay during wich the user will be considered as still active
        $delay = new \DateTime($minutes . ' minutes ago');

        return ( $this->getLastActivityAt() > $delay );
    }

    /**
     * @return Bool Whether the user is new or not
     */
    public function isNew($days = 30)
    {
        // Delay during wich the user will be considered as still new
        $delay = new \DateTime($days .' days ago');

        return ( $this->getSignUpDate() > $delay );
    }

    /**
     * Set isNonLocked
     *
     * @param boolean $isNonLocked
     * @return User
     */
    public function setIsNonLocked($isNonLocked)
    {
        $this->isNonLocked = $isNonLocked;

        return $this;
    }

    /**
     * Get isNonLocked
     *
     * @return boolean 
     */
    public function getIsNonLocked()
    {
        return $this->isNonLocked;
    }

    /**
     * Set signUpDate
     *
     * @param \DateTime $signUpDate
     * @return User
     */
    public function setSignUpDate($signUpDate)
    {
        $this->signUpDate = $signUpDate;

        return $this;
    }

    /**
     * Get signUpDate
     *
     * @return \DateTime 
     */
    public function getSignUpDate()
    {
        return $this->signUpDate;
    }

    /**
     * Get role
     *
     * @return \AppBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return User
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add viewed
     *
     * @param \AppBundle\Entity\View $viewed
     * @return User
     */
    public function addViewed(\AppBundle\Entity\View $viewed)
    {
        $this->viewed[] = $viewed;

        return $this;
    }

    /**
     * Remove viewed
     *
     * @param \AppBundle\Entity\View $viewed
     */
    public function removeViewed(\AppBundle\Entity\View $viewed)
    {
        $this->viewed->removeElement($viewed);
    }

    /**
     * Get viewed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * Add viewedMe
     *
     * @param \AppBundle\Entity\View $viewedMe
     * @return User
     */
    public function addViewedMe(\AppBundle\Entity\View $viewedMe)
    {
        $this->viewedMe[] = $viewedMe;

        return $this;
    }

    /**
     * Remove viewedMe
     *
     * @param \AppBundle\Entity\View $viewedMe
     */
    public function removeViewedMe(\AppBundle\Entity\View $viewedMe)
    {
        $this->viewedMe->removeElement($viewedMe);
    }

    /**
     * Get viewedMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViewedMe()
    {
        return $this->viewedMe;
    }

    /**
     * Add contacted
     *
     * @param \AppBundle\Entity\Contact $contacted
     * @return User
     */
    public function addContacted(\AppBundle\Entity\Contact $contacted)
    {
        $this->contacted[] = $contacted;

        return $this;
    }

    /**
     * Remove contacted
     *
     * @param \AppBundle\Entity\Contact $contacted
     */
    public function removeContacted(\AppBundle\Entity\Contact $contacted)
    {
        $this->contacted->removeElement($contacted);
    }

    /**
     * Get contacted
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContacted()
    {
        return $this->contacted;
    }

    /**
     * Add contactedMe
     *
     * @param \AppBundle\Entity\Contact $contactedMe
     * @return User
     */
    public function addContactedMe(\AppBundle\Entity\Contact $contactedMe)
    {
        $this->contactedMe[] = $contactedMe;

        return $this;
    }

    /**
     * Remove contactedMe
     *
     * @param \AppBundle\Entity\Contact $contactedMe
     */
    public function removeContactedMe(\AppBundle\Entity\Contact $contactedMe)
    {
        $this->contactedMe->removeElement($contactedMe);
    }

    /**
     * Get contactedMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactedMe()
    {
        return $this->contactedMe;
    }

    /**
     * Add favorited
     *
     * @param \AppBundle\Entity\Favorite $favorited
     * @return User
     */
    public function addFavorited(\AppBundle\Entity\Favorite $favorited)
    {
        $this->favorited[] = $favorited;

        return $this;
    }

    /**
     * Remove favorited
     *
     * @param \AppBundle\Entity\Favorite $favorited
     */
    public function removeFavorited(\AppBundle\Entity\Favorite $favorited)
    {
        $this->favorited->removeElement($favorited);
    }

    /**
     * Get favorited
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavorited()
    {
        return $this->favorited;
    }

    public function isAddFavorite($userId)
    {
        $res = false;
        if($userId == $this->getId()){
            $res = true;
        }else {
            foreach ($this->favorited as $favorite) {
                if ($favorite->getMember()->getId() == $userId) {
                    $res = true;
                }
            }
        }
        return $res;
    }

    /**
     * Add favoritedMe
     *
     * @param \AppBundle\Entity\Favorite $favoritedMe
     * @return User
     */
    public function addFavoritedMe(\AppBundle\Entity\Favorite $favoritedMe)
    {
        $this->favoritedMe[] = $favoritedMe;

        return $this;
    }

    /**
     * Remove favoritedMe
     *
     * @param \AppBundle\Entity\Favorite $favoritedMe
     */
    public function removeFavoritedMe(\AppBundle\Entity\Favorite $favoritedMe)
    {
        $this->favoritedMe->removeElement($favoritedMe);
    }

    /**
     * Get favoritedMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavoritedMe()
    {
        return $this->favoritedMe;
    }

    /**
     * Add blackListed
     *
     * @param \AppBundle\Entity\BlackList $blackListed
     * @return User
     */
    public function addBlackListed(\AppBundle\Entity\BlackList $blackListed)
    {
        $this->blackListed[] = $blackListed;

        return $this;
    }

    /**
     * Remove blackListed
     *
     * @param \AppBundle\Entity\BlackList $blackListed
     */
    public function removeBlackListed(\AppBundle\Entity\BlackList $blackListed)
    {
        $this->blackListed->removeElement($blackListed);
    }

    /**
     * Get blackListed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlackListed()
    {
        return $this->blackListed;
    }

    public function isAddBlackListed($userId)
    {
        $res = false;
        if($userId == $this->getId()){
            $res = true;
        }else {
            foreach ($this->blackListed as $blackList) {
                if ($blackList->getMember()->getId() == $userId) {
                    $res = true;
                }
            }
        }
        return $res;
    }

    /**
     * Add blackListedMe
     *
     * @param \AppBundle\Entity\BlackList $blackListedMe
     * @return User
     */
    public function addBlackListedMe(\AppBundle\Entity\BlackList $blackListedMe)
    {
        $this->blackListedMe[] = $blackListedMe;

        return $this;
    }

    /**
     * Remove blackListedMe
     *
     * @param \AppBundle\Entity\BlackList $blackListedMe
     */
    public function removeBlackListedMe(\AppBundle\Entity\BlackList $blackListedMe)
    {
        $this->blackListedMe->removeElement($blackListedMe);
    }

    /**
     * Get blackListedMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlackListedMe()
    {
        return $this->blackListedMe;
    }

    /**
     * Set isFrozen
     *
     * @param boolean $isFrozen
     * @return User
     */
    public function setIsFrozen($isFrozen)
    {
        $this->isFrozen = $isFrozen;

        return $this;
    }

    /**
     * Get isFrozen
     *
     * @return boolean 
     */
    public function getIsFrozen()
    {
        return $this->isFrozen;
    }

    /**
     * Set freezeReason
     *
     * @param string $freezeReason
     * @return User
     */
    public function setFreezeReason($freezeReason)
    {
        $this->freezeReason = $freezeReason;

        return $this;
    }

    /**
     * Get freezeReason
     *
     * @return string 
     */
    public function getFreezeReason()
    {
        return $this->freezeReason;
    }

    /**
     * Set banReason
     *
     * @param string $banReason
     * @return User
     */
    public function setBanReason($banReason)
    {
        $this->banReason = $banReason;

        return $this;
    }

    /**
     * Get banReason
     *
     * @return string 
     */
    public function getBanReason()
    {
        return $this->banReason;
    }

    /**
     * Set isFlagged
     *
     * @param boolean $isFlagged
     * @return User
     */
    public function setIsFlagged($isFlagged)
    {
        $this->isFlagged = $isFlagged;

        return $this;
    }

    /**
     * Get isFlagged
     *
     * @return boolean 
     */
    public function getIsFlagged()
    {
        return $this->isFlagged;
    }

    /**
     * Set startSubscription
     *
     * @param \DateTime $startSubscription
     * @return User
     */
    public function setStartSubscription($startSubscription)
    {
        $this->startSubscription = $startSubscription;

        return $this;
    }

    /**
     * Get startSubscription
     *
     * @return \DateTime
     */
    public function getStartSubscription()
    {
        return $this->startSubscription;
    }

    /**
     * Set endSubscription
     *
     * @param \DateTime $endSubscription
     * @return User
     */
    public function setEndSubscription($endSubscription)
    {
        $this->endSubscription = $endSubscription;

        return $this;
    }

    /**
     * Get endSubscription
     *
     * @return \DateTime
     */
    public function getEndSubscription()
    {
        return $this->endSubscription;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer 
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set adminComments
     *
     * @param string $adminComments
     * @return User
     */
    public function setAdminComments($adminComments)
    {
        $this->adminComments = $adminComments;

        return $this;
    }

    /**
     * Get adminComments
     *
     * @return string 
     */
    public function getAdminComments()
    {
        return $this->adminComments;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return User
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
     * Set loginFrom
     *
     * @param \AppBundle\Entity\LoginFrom $loginFrom
     * @return User
     */
    public function setLoginFrom(\AppBundle\Entity\LoginFrom $loginFrom = null)
    {
        $this->loginFrom = $loginFrom;

        return $this;
    }

    /**
     * Get loginFrom
     *
     * @return \AppBundle\Entity\LoginFrom 
     */
    public function getLoginFrom()
    {
        return $this->loginFrom;
    }

    /**
     * Set zodiac
     *
     * @param \AppBundle\Entity\Zodiac $zodiac
     * @return User
     */
    public function setZodiac(\AppBundle\Entity\Zodiac $zodiac = null)
    {
        $this->zodiac = $zodiac;

        return $this;
    }

    /**
     * Get zodiac
     *
     * @return \AppBundle\Entity\Zodiac 
     */
    public function getZodiac()
    {
        return $this->zodiac;
    }

    /**
     * Set ethnicity
     *
     * @param \AppBundle\Entity\Ethnicity $ethnicity
     * @return User
     */
    public function setEthnicity(\AppBundle\Entity\Ethnicity $ethnicity = null)
    {
        $this->ethnicity = $ethnicity;

        return $this;
    }

    /**
     * Get ethnicity
     *
     * @return \AppBundle\Entity\Ethnicity 
     */
    public function getEthnicity()
    {
        return $this->ethnicity;
    }

    /**
     * Set isOnHomepage
     *
     * @param boolean $isOnHomepage
     * @return User
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
     * Set lastloginAt
     *
     * @param \DateTime $lastloginAt
     * @return User
     */
    public function setLastloginAt($lastloginAt)
    {
        $this->lastloginAt = $lastloginAt;

        return $this;
    }

    /**
     * Get lastloginAt
     *
     * @return \DateTime 
     */
    public function getLastloginAt()
    {
        return $this->lastloginAt;
    }

    /**
     * Set lastRealActivityAt
     *
     * @param \DateTime $lastRealActivityAt
     * @return User
     */
    public function setLastRealActivityAt($lastRealActivityAt)
    {
        $this->lastRealActivityAt = $lastRealActivityAt;

        return $this;
    }

    /**
     * Get lastRealActivityAt
     *
     * @return \DateTime 
     */
    public function getLastRealActivityAt()
    {
        return $this->lastRealActivityAt;
    }

    /**
     * Add connected
     *
     * @param \AppBundle\Entity\Communication $connected
     * @return User
     */
    public function addConnected(\AppBundle\Entity\Communication $connected)
    {
        $this->connected[] = $connected;

        return $this;
    }

    /**
     * Remove connected
     *
     * @param \AppBundle\Entity\Communication $connected
     */
    public function removeConnected(\AppBundle\Entity\Communication $connected)
    {
        $this->connected->removeElement($connected);
    }

    /**
     * Get connected
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Add connectedMe
     *
     * @param \AppBundle\Entity\Communication $connectedMe
     * @return User
     */
    public function addConnectedMe(\AppBundle\Entity\Communication $connectedMe)
    {
        $this->connectedMe[] = $connectedMe;

        return $this;
    }

    /**
     * Remove connectedMe
     *
     * @param \AppBundle\Entity\Communication $connectedMe
     */
    public function removeConnectedMe(\AppBundle\Entity\Communication $connectedMe)
    {
        $this->connectedMe->removeElement($connectedMe);
    }

    /**
     * Get connectedMe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConnectedMe()
    {
        return $this->connectedMe;
    }

    /**
     * Set oldPassword
     *
     * @param string $oldPassword
     * @return User
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * Get oldPassword
     *
     * @return string
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * Set zipCode
     *
     * @param \AppBundle\Entity\ZipCode $zipCode
     *
     * @return User
     */
    public function setZipCode(\AppBundle\Entity\ZipCode $zipCode = null)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return \AppBundle\Entity\ZipCode
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }



    /**
     * Add hobby
     *
     * @param \AppBundle\Entity\Hobby $hobby
     *
     * @return User
     */
    public function addHobby(\AppBundle\Entity\Hobby $hobby)
    {
        $this->hobbies[] = $hobby;

        return $this;
    }

    /**
     * Remove hobby
     *
     * @param \AppBundle\Entity\Hobby $hobby
     */
    public function removeHobby(\AppBundle\Entity\Hobby $hobby)
    {
        $this->hobbies->removeElement($hobby);
    }

    /**
     * Get hobbies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHobbies()
    {
        return $this->hobbies;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set code
     *
     * @param integer $code
     * @return User
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add notification
     *
     * @param \AppBundle\Entity\UserNotifications $notification
     *
     * @return User
     */
    public function addNotification(\AppBundle\Entity\UserNotifications $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \AppBundle\Entity\UserNotifications $notification
     */
    public function removeNotification(\AppBundle\Entity\UserNotifications $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        foreach ($this->notifications as $notification){
            if($notification->getLikeMe()->getIsBingo() and $notification->getNotification()->getId() == 1){
                $this->removeNotification($notification);
            }
        }
        return $this->notifications;
    }

    /**
     * Add like
     *
     * @param \AppBundle\Entity\LikeMe $like
     *
     * @return User
     */
    public function addLike(\AppBundle\Entity\LikeMe $like)
    {
        $this->likes[] = $like;

        return $this;
    }

    /**
     * Remove like
     *
     * @param \AppBundle\Entity\LikeMe $like
     */
    public function removeLike(\AppBundle\Entity\LikeMe $like)
    {
        $this->likes->removeElement($like);
    }

    /**
     * Get likes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Add likeMe
     *
     * @param \AppBundle\Entity\LikeMe $like
     *
     * @return User
     */
    public function addLikeMe(\AppBundle\Entity\LikeMe $like)
    {
        $this->likesMe[] = $like;

        return $this;
    }

    /**
     * Remove likeMe
     *
     * @param \AppBundle\Entity\LikeMe $like
     */
    public function removeLikeMe(\AppBundle\Entity\LikeMe $like)
    {
        $this->likesMe->removeElement($like);
    }

    /**
     * Get likesMe
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLikesMe()
    {
        return $this->likesMe;
    }

    public function isAddLike(\AppBundle\Entity\User $user)
    {
        $userId = $user->getId();
        $res = false;
        if($userId == $this->getId() or $this->getMainPhoto() === null or $user->getMainPhoto() === null){
            $res = true;
        }else {
            foreach ($this->likes as $like) {
                if ($like->getUserTo()->getId() == $userId) {
                    $res = true;
                }
            }
        }
        return $res;
    }

    /**
     * Set isSentEmail
     *
     * @param boolean $isSentEmail
     *
     * @return User
     */
    public function setIsSentEmail($isSentEmail)
    {
        $this->isSentEmail = $isSentEmail;

        return $this;
    }

    /**
     * Get isSentEmail
     *
     * @return boolean
     */
    public function getIsSentEmail()
    {
        return $this->isSentEmail;
    }

    /**
     * Set isSentPush
     *
     * @param boolean $isSentPush
     *
     * @return User
     */
    public function setIsSentPush($isSentPush)
    {
        $this->isSentPush = $isSentPush;

        return $this;
    }

    /**
     * Get isSentPush
     *
     * @return boolean
     */
    public function getIsSentPush()
    {
        return $this->isSentPush;
    }

    /**
     * Set smsCount
     *
     * @param integer $smsCount
     *
     * @return User
     */
    public function setSmsCount($smsCount)
    {
        $this->smsCount = $smsCount;

        return $this;
    }

    /**
     * Get smsCount
     *
     * @return integer
     */
    public function getSmsCount()
    {
        return $this->smsCount;
    }

    /**
     * Add likesMe
     *
     * @param \AppBundle\Entity\LikeMe $likesMe
     *
     * @return User
     */
    public function addLikesMe(\AppBundle\Entity\LikeMe $likesMe)
    {
        $this->likesMe[] = $likesMe;

        return $this;
    }

    /**
     * Remove likesMe
     *
     * @param \AppBundle\Entity\LikeMe $likesMe
     */
    public function removeLikesMe(\AppBundle\Entity\LikeMe $likesMe)
    {
        $this->likesMe->removeElement($likesMe);
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return User
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return User
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }


}
