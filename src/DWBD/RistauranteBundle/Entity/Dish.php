<?php

namespace DWBD\RistauranteBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DWBD\SecurityBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dish
 *
 * @ORM\Table(name="dish")
 * @ORM\Entity(repositoryClass="DWBD\RistauranteBundle\Repository\DishRepository")
 * @ORM\HasLifecycleCallbacks()
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
	 * @ORM\Column(name="title", type="string", length=80, unique=true)
	 *
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Length(min="5", max="80")
	 * @Assert\Type(type="string")
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 *
	 * @Assert\Length(max="6000", min="15")
	 * @Assert\Type(type="string")
	 */
	private $description;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="price", type="float")
	 *
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="float")
	 * @Assert\GreaterThan(value="0.0")
	 * @Assert\LessThan(value="300.0")
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
	 *
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="integer")
	 */
	private $state;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="homemade", type="boolean")
	 *
	 * @Assert\Type(type="bool")
	 */
	private $homemade;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="image", type="text", nullable=true)
	 *
	 */
	private $image;

	/**
	 * @Assert\Image(
	 *     mimeTypes={"image/bmp", "image/png", "image/gif", "image/jpg", "image/jpeg"},
	 *     mimeTypesMessage="Only bmp, gif, png and jpg allowed",
	 *     maxSize="20M",
	 *     maxSizeMessage="20M maximum"
	 * )
	 */
	private $file;

	/** @var  string */
	private $temp;

	/**
	 * @var int
	 *
	 * @ORM\Column(name="category", type="integer", nullable=true)
	 * @Enum({
	 *        CategoryEnum::ENTREE,
	 *        CategoryEnum::DISH,
	 *        CategoryEnum::DESSERT,
	 *        CategoryEnum::CHEESE_PLATE,
	 *        CategoryEnum::APPETIZER
	 * })
	 *
	 * @Assert\Type(type="integer")
	 */
	private $category;

	/**
	 * @var array
	 *
	 * @ORM\Column(name="allergens", type="json_array", nullable=true)
	 *
	 * @Assert\Type(type="array")
	 */
	private $allergens;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="has_been_refused_or_validated", type="boolean")
	 */
	private $hasBeenRefusedOrValidated;

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
	 * @ORM\ManyToMany(targetEntity="Menu", mappedBy="dishes", orphanRemoval=true)
	 */
	private $menus;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->menus = new ArrayCollection();
		$this->hasBeenRefusedOrValidated = false;
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
	 *
	 * @return Dish
	 */
	public function setAuthor($author)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getMenus()
	{
		return $this->menus;
	}

	/**
	 * @return bool
	 */
	public function hasBeenRefusedOrValidated()
	{
		return $this->hasBeenRefusedOrValidated;
	}

	/**
	 * Check if the dish has been refused or validated
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

	/*
	 *
	 */

	/**
	 * Get file.
	 *
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Set file.
	 *
	 * @param UploadedFile $file
	 */
	public function setFile(UploadedFile $file)
	{
		$this->file = $file;
	}

	public function getAbsolutePath()
	{
		return null === $this->image
			? null
			: $this->getUploadRootDir() . '/' . $this->image;
	}

	protected function getUploadRootDir()
	{
		return __DIR__ . '/../../../../web/' . $this->getUploadDir();
	}

	protected function getUploadDir()
	{
		return 'img/dishes';
	}

	/**
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 */
	public function preUpload()
	{
		if (null !== $this->getFile()) {
			$filename = sha1(uniqid(mt_rand(), true));
			$this->image = $filename . '.' . $this->getFile()->guessExtension();
		}
	}

	/**
	 * @ORM\PostPersist()
	 * @ORM\PostUpdate()
	 */
	public function upload()
	{
		if (null === $this->getFile()) {
			return;
		}

		// if there is an error an exception will be thrown
		$this->getFile()->move($this->getUploadRootDir(), $this->image);

		// check if we have an old image
		if (isset($this->temp)) {
			@unlink($this->getUploadRootDir() . '/' . $this->temp);
			$this->temp = null;
		}
		$this->file = null;
	}

	/**
	 * @ORM\PostRemove()
	 */
	public function removeUpload()
	{
		if ($file = $this->getAbsolutePath()) {
			@unlink($file);
		}
	}
}

