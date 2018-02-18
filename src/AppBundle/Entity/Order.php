<?php

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator as CustomAssert;

/**
 * Order
 *
 * @ORM\Table(name="order_tickets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderRepository")
 *
 * @CustomAssert\AllowFullDay(groups={"step1"}) *
 * @CustomAssert\NotMaxTicketsSold(groups={"step1"})
 */
class Order
{
    const ORDER_FULL_DAY = true;
    const ORDER_HALF_DAY = false;
    const MAX_TICKETS_FOR_1_DATE = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Ticket[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ticket", mappedBy="order", cascade={"persist"})
     *
     * @Assert\Valid()
     */
    private $tickets;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="order_date", type="datetime")
     *
     * @Assert\DateTime()
     */
    private $orderDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_visit", type="datetime")
     *
     * @Assert\Date(groups={"step1"})
     * @Assert\Range(
     *     min = "today",
     *     max = "+9 months",
     *     minMessage = "order.dateOfVisit.past",
     *     maxMessage = "order.dateOfVisit.max",
     *     groups={"step1"}
     * )
     * @CustomAssert\NotClosingDay(groups={"step1"})
     * @CustomAssert\NotHoliday(groups={"step1"})
     */
    private $dateOfVisit;

    /**
     * @var bool
     *
     * @ORM\Column(name="fullDay", type="boolean")
     */
    private $fullDay;

    /**
     * @var float
     *
     * @ORM\Column(name="total_amount", type="float")
     */
    private $totalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(
     *     message="order.reference.blank"
     * )
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Assert\Email(checkMX = true, groups={"step1"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="trans_id", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="order.transaction.empty"
     * )
     */
    private $transId;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_tickets", type="integer")
     *
     * @Assert\Range(
     *     min = 1,
     *     max = 10,
     *     minMessage = "order.nbtickets.min",
     *     maxMessage = "order.nbtickets.max",
     *     groups={"step1"}
     * )
     */
    private $nbTickets;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderDate
     *
     * @param \DateTime $date
     *
     * @return Order
     */
    public function setOrderDate($date)
    {
        $this->orderDate = $date;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set dateOfVisit
     *
     * @param \DateTime $date
     *
     * @return Order
     */
    public function setDateOfVisit($date)
    {
        $this->dateOfVisit = $date;

        return $this;
    }

    /**
     * Set fullDay
     *
     * @param boolean $fullDay
     *
     * @return Ticket
     */
    public function setFullDay($fullDay)
    {
        $this->fullDay = $fullDay;

        return $this;
    }

    /**
     * Get fullDay
     *
     * @return bool
     */
    public function getFullDay()
    {
        return $this->fullDay;
    }

    /**
     * Get dateOfVisit
     *
     * @return \DateTime
     */
    public function getDateOfVisit()
    {
        return $this->dateOfVisit;
    }

    /**
     * Set totalAmount
     *
     * @param float $totalAmount
     *
     * @return Order
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Order
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Order
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
     * Set transId
     *
     * @param string $transId
     *
     * @return Order
     */
    public function setTransId($transId)
    {
        $this->transId = $transId;

        return $this;
    }

    /**
     * Get transId
     *
     * @return string
     */
    public function getTransId()
    {
        return $this->transId;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDateOfVisit(new \DateTime());
        $this->setTotalAmount(0);
        $this->tickets = new ArrayCollection();
    }

    /**
     * Add ticket
     *
     * @param Ticket $ticket
     *
     * @return Order
     */
    public function addTicket(Ticket $ticket)
    {
        $this->tickets[] = $ticket;
        $ticket->setOrder($this);

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param Ticket $ticket
     */
    public function removeTicket(Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * Set nbTickets
     *
     * @param integer $nbTickets
     *
     * @return Order
     */
    public function setNbTickets($nbTickets)
    {
        $this->nbTickets = $nbTickets;

        return $this;
    }

    /**
     * Get nbTickets
     *
     * @return integer
     */
    public function getNbTickets()
    {
        return $this->nbTickets;
    }
}
