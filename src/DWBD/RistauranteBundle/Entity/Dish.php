<?php

namespace DWBD\RistauranteBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DWBD\SecurityBundle\Entity\User;

/**
 * Dish
 *
 * @ORM\Table(name="dish")
 * @ORM\Entity(repositoryClass="DWBD\RistauranteBundle\Repository\DishRepository")
 */
class Dish
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

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
     */
    private $state;

    /**
     * @var bool
     *
     * @ORM\Column(name="homemade", type="boolean")
     */
    private $homemade;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="image", type="text")
	 */
    private $image;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="category", type="integer")
	 * @Enum({
	 *		CategoryEnum::ENTREE,
	 *     	CategoryEnum::DISH,
	 *     	CategoryEnum::DESSERT,
	 *     	CategoryEnum::CHEESE_PLATE,
	 *     	CategoryEnum::APPETIZER
	 * })
	 */
    private $category;

	/**
	 * @var array
	 *
	 * @ORM\Column(name="allergens", type="array")
	 */
    private $allergens;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="DWBD\SecurityBundle\Entity\User", inversedBy="dishes")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
    private $author;

	/**
	 * @var ArrayCollection<Menu>
	 *
	 * @ORM\ManyToMany(targetEntity="Menu", mappedBy="dishes")
	 */
    private $menus;


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
     * @return Dish
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
     *
     * @return Dish
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
     * Set price
     *
     * @param float $price
     *
     * @return Dish
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
     * Set state
     *
     * @param integer $state
     *
     * @return Dish
     */
    public function setState($state)
    {
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
     * Set homemade
     *
     * @param boolean $homemade
     *
     * @return Dish
     */
    public function setHomemade($homemade)
    {
        $this->homemade = $homemade;

        return $this;
    }

    /**
     * Get homemade
     *
     * @return bool
     */
    public function isHomemade()
    {
        return $this->homemade;
    }

	/**
	 * @return string
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image)
	{
		$this->image = $image;
	}

	/**
	 * @return int
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param int $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * @return array
	 */
	public function getAllergens()
	{
		return $this->allergens;
	}

	/**
	 * @param array $allergens
	 */
	public function setAllergens($allergens)
	{
		$this->allergens = $allergens;
	}

	/**
	 * @return User
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param User $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getMenus()
	{
		return $this->menus;
	}

	/**
	 * @param ArrayCollection $menus
	 */
	public function setMenus($menus)
	{
		$this->menus = $menus;
	}

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }

    /**
     * Get homemade
     *
     * @return boolean
     */
    public function getHomemade()
    {
        return $this->homemade;
    }

    /**
     * Add menu
     *
     * @param Menu $menu
     *
     * @return Dish
     */
    public function addMenu(Menu $menu)
    {
        $this->menus[] = $menu;

        return $this;
    }

    /**
     * Remove menu
     *
     * @param Menu $menu
     */
    public function removeMenu(Menu $menu)
    {
        $this->menus->removeElement($menu);
    }
}
