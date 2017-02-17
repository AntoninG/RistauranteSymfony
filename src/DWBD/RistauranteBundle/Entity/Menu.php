<?php

namespace DWBD\RistauranteBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DWBD\SecurityBundle\Entity\User;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="DWBD\RistauranteBundle\Repository\MenuRepository")
 * @ORM\EntityListeners({"DWBD\RistauranteBundle\Entity\Listener\MenuMailListener"})
 */
class Menu
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
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string", length=80, unique=true)
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Length(min="5", max="80")
	 * @Assert\Type(type="string")
	 */
	private $title;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="price", type="float")
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="float"),
	 * @Assert\GreaterThan(value="0.0")
	 * @Assert\LessThan(value="3000.0")
	 */
	private $price;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="displayOrder", type="integer")
	 *
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="integer")
	 * @Assert\GreaterThanOrEqual(value="1")
	 * @Assert\LessThanOrEqual(value="45")
	 */
	private $displayOrder;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="state", type="integer")
	 * @Enum({
	 *     StateEnum::STATE_DRAFT,
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
	 * @var boolean
	 *
	 * @ORM\Column(name="has_been_refused_or_validated", type="boolean")
	 */
	private $hasBeenRefusedOrValidated;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="DWBD\SecurityBundle\Entity\User", inversedBy="menus")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $author;

	/**
	 * @var ArrayCollection<Menu>
	 *
	 * @ORM\ManyToMany(targetEntity="Dish", inversedBy="menus", cascade={"persist"}, orphanRemoval=true)
	 * @ORM\JoinTable(name="menus_dishes")
	 */
	private $dishes;

	/** @var int  */
	private $previousState;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->dishes = new ArrayCollection();
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
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Menu
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
	 * Set price
	 *
	 * @param float $price
	 *
	 * @return Menu
	 */
	public function setPrice($price)
	{
		$this->price = $price;

		return $this;
	}

	/**
	 * Get price
	 *
	 * @return float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * Set displayOrder
	 *
	 * @param integer $displayOrder
	 *
	 * @return Menu
	 */
	public function setDisplayOrder($displayOrder)
	{
		$this->displayOrder = $displayOrder;

		return $this;
	}

	/**
	 * Get displayOrder
	 *
	 * @return int
	 */
	public function getDisplayOrder()
	{
		return $this->displayOrder;
	}

	/**
	 * Set state
	 *
	 * @param integer $state
	 *
	 * @return Menu
	 */
	public function setState($state)
	{
		$this->previousState = isset($this->state) ? $this->state : null;
		$this->state = $state;

		return $this;
	}

	/**
	 * Get state
	 *
	 * @return int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * Get previous state
	 *
	 * @return int
	 */
	public function getPreviousState()
	{
		return $this->previousState;
	}

	/**
	 * @return bool
	 */
	public function hasBeenRefusedOrValidated()
	{
		return $this->hasBeenRefusedOrValidated;
	}

	/**
	 * Check if the menu has been refused or validated
	 * If true, put the flag hasBeenRefusedOrValidated to true
	 *
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 *
	 */
	public function checkHasBeenRefusedOrValidated()
	{
		if (in_array($this->state, array(StateEnum::STATE_REFUSED, StateEnum::STATE_VALIDATED)) && !$this->hasBeenRefusedOrValidated) {
			$this->hasBeenRefusedOrValidated = true;
		}
	}

	/**
	 * Set author
	 *
	 * @param User $author
	 *
	 * @return Menu
	 */
	public function setAuthor(User $author = null)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return User
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Add dish
	 *
	 * @param Dish $dish
	 *
	 * @return Menu
	 */
	public function addDish(Dish $dish)
	{
		$this->dishes[] = $dish;

		return $this;
	}

	/**
	 * Remove dish
	 *
	 * @param Dish $dish
	 */
	public function removeDish(Dish $dish)
	{
		$this->dishes->removeElement($dish);
	}

	/**
	 * Get dishes
	 *
	 * @return ArrayCollection
	 */
	public function getDishes()
	{
		return $this->dishes;
	}

}
