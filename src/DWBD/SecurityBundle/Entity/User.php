<?php

namespace DWBD\SecurityBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\Menu;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="DWBD\SecurityBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="username", type="string", length=60, unique=true)
	 *
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="string")
	 * @Assert\Length(
	 *     min=5,
	 *     max=60
	 * )
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=72)
	 *
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type(type="string")
	 * @Assert\Length(
	 *     min="8",
	 *     max="72"
	 * )
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
	 *
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Email()
     */
    private $email;

	/**
	 * @var array
	 *
	 * @ORM\Column(
	 *     name="roles",
	 *     type="json_array",
	 *     length=40
	 * )
	 *
	 * @Enum({RoleEnum::USER, RoleEnum::WAITER, RoleEnum::EDITOR, RoleEnum::REVIEWER, RoleEnum::CHIEF, RoleEnum::ADMIN})
	 * @Required()
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 */
    private $roles;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="salt", type="string", length=255)
	 */
    private $salt;

    /**
     * @var bool
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

	/**
	 * @var ArrayCollection<Dish>
	 *
	 * @ORM\OneToMany(targetEntity="DWBD\RistauranteBundle\Entity\Dish", mappedBy="author", cascade={"persist"})
	 */
    private $dishes;

	/**
	 * @var ArrayCollection<Menu>
	 *
	 * @ORM\OneToMany(targetEntity="DWBD\RistauranteBundle\Entity\Menu", mappedBy="author", cascade={"persist"})
	 */
    private $menus;

    public function __construct()
	{
		$this->isActive = true;
		$this->salt = md5(uniqid(null, true));
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
     * Set username
     *
     * @param string $username
     *
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
     *
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
     *
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
	 * @return bool
	 */
	public function isActive()
	{
		return $this->isActive;
	}

	/**
	 * @param bool $isActive
	 * @return User
	 */
	public function setActive($isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}



	/**
	 * Set dishes
	 *
	 * @param ArrayCollection $dishes
	 *
	 * @return User
	 */
	public function setDishes(ArrayCollection $dishes)
	{
		$this->dishes = $dishes;

		return $this;
	}

	/**
	 * Get dishes
	 *
	 * @return ArrayCollection<Dish>
	 */
	public function getDishes()
	{
		return $this->dishes;
	}

	/**
	 * @param Dish $dish
	 *
	 * @return User
	 */
	public function addDish(Dish $dish)
	{
		$this->dishes->add($dish);

		return $this;
	}

	/**
	 * Set menus
	 *
	 * @param ArrayCollection $menus
	 *
	 * @return User
	 */
	public function setMenus(ArrayCollection $menus)
	{
		$this->menus = $menus;

		return $this;
	}

	/**
	 * Get menus
	 *
	 * @return ArrayCollection<Menu>
	 */
	public function getMenus()
	{
		return $this->menus;
	}

	/**
	 * @param Menu $menu
	 *
	 * @return User
	 */
	public function addMenu(Menu $menu)
	{
		$this->menus->add($menu);

		return $this;
	}

	/**
	 * Get the user's roles
	 *
	 * @return array
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * Set the user's roles
	 *
	 * @param array $roles
	 */
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}


	/**
	 * Get the user's salt
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	public function eraseCredentials()
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array(
			$this->id,
			$this->username,
			$this->password,
			$this->salt,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->password,
			$this->salt
		) = unserialize($serialized);
	}
}

