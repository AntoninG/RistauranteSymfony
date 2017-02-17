<?php

namespace DWBD\RistauranteBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="DWBD\RistauranteBundle\Repository\ReservationRepository")
 * @ORM\EntityListeners({"DWBD\RistauranteBundle\Entity\Listener\ReservationListener"})
 */
class Reservation
{
	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date", type="date")
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 */
	private $date;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="time", type="time")
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 */
	private $time;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="number", type="integer")
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="integer")
	 * @Assert\GreaterThan(value="0")
	 */
	private $number;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=255)
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="string")
	 * @Assert\Length(min="5", max="255")
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="phone", type="string", length=16)
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="string")
	 * @Assert\Length(min="10", max="16")
	 */
	private $phone;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="state", type="boolean")
	 * @Enum({
	 *     StateEnum::STATE_WAITING,
	 *     StateEnum::STATE_REFUSED,
	 *     StateEnum::STATE_VALIDATED
	 * })
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="integer")
	 */
	private $state;

	/**
	 * Reservation constructor.
	 */
	public function __construct()
	{
		$this->state = StateEnum::STATE_WAITING;
	}


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
	 * Set date
	 *
	 * @param \DateTime $date
	 *
	 * @return Reservation
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
	 * Set time
	 *
	 * @param \DateTime $time
	 *
	 * @return Reservation
	 */
	public function setTime($time)
	{
		$this->time = $time;

		return $this;
	}

	/**
	 * Get time
	 *
	 * @return \DateTime
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * Set number
	 *
	 * @param integer $number
	 *
	 * @return Reservation
	 */
	public function setNumber($number)
	{
		$this->number = $number;

		return $this;
	}

	/**
	 * Get number
	 *
	 * @return int
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Reservation
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
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Reservation
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
	 * Set phone
	 *
	 * @param string $phone
	 *
	 * @return Reservation
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
	 * @return int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param int $state
	 * @return Reservation
	 */
	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}


}

