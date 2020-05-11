<?php
/**
 * Created by PhpStorm.
 * User: interdate
 * Date: 18/12/2018
 * Time: 9:08
 */

namespace AppBundle\Entity;

use AppBundle\AppBundle;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\Criteria;

/**
 * PaymentHistory
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PaymentHistory
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
     * @ORM\ManyToMany(targetEntity="User", mappedBy="paymentHistory")
     **/
    private $users;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_date", type="date")
     */
    private $paymentDate;

    /**
     * @ORM\OneToMany(targetEntity="PaymentHistory", mappedBy="parent")
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="PaymentHistory", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string", length=50, nullable=true)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="recurring_id", type="string", length=50, nullable=true)
     */
    private $recurringId;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=15, nullable=true)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="full_data", type="text")
     */
    private $fullData;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text")
     */
    private $note;



    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add users
     *
     * @param \AppBundle\Entity\User $users
     * @return PaymentHistory
     */
    public function addUser(\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \AppBundle\Entity\User $users
     */
    public function removeUser(\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     * @return PaymentHistory
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set transactionId
     *
     * @param string $transactionId
     * @return PaymentHistory
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set recurringId
     *
     * @param string $recurringId
     * @return PaymentHistory
     */
    public function setRecurringId($recurringId)
    {
        $this->recurringId = $recurringId;

        return $this;
    }

    /**
     * Get recurringId
     *
     * @return string
     */
    public function getRecurringId()
    {
        return $this->recurringId;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return PaymentHistory
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set fullData
     *
     * @param array $fullData
     * @return PaymentHistory
     */
    public function setFullData($fullData)
    {
        $fullData = serialize((array)$fullData);
        $this->fullData = $fullData;

        return $this;
    }

    /**
     * Get fullData
     *
     * @return array
     */
    public function getFullData()
    {
        $dc2array = unserialize($this->fullData);
        return $dc2array;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return PaymentHistory
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }


    public function getParent() {
        return $this->parent;
    }

    public function getChildren() {
        return $this->children;
    }

    // always use this to setup a new parent/child relationship
    public function addChild(PaymentHistory $child) {
        $this->children[] = $child;
        $child->setParent($this);
    }

    public function setParent(PaymentHistory $parent) {
        $this->parent = $parent;
    }

    public function getEndDate(){
        $endDate = new \DateTime($this->paymentDate->format('Y-m-d H:i:s'));
        $ppdata = $this->getFullData();
        $custom = explode("_",$ppdata['custom']);
        $per = $custom[1];
        $strPer = 'P14D';
        if($per == 1){
            $strPer = 'P1Y';
        }elseif($per == 2){
            $strPer = 'P6M';
        }elseif($per == 3){
            $strPer = 'P3M';
        }elseif($per == 4){
            $strPer = 'P1M';
        }
        $endDate->add(new \DateInterval($strPer));
        return $endDate;
    }

}