<?php

namespace DWBD\RistauranteBundle\Entity\Listener;

use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PostUpdate;
use Doctrine\ORM\Mapping\PostRemove;
use DWBD\RistauranteBundle\Entity\Dish;

class DishFileListener
{
	/** @var  string */
	private $dishesDirectory;

	public function __construct($dishesDirectory)
	{

		$this->dishesDirectory = $dishesDirectory;
	}

	/**
	 * @PrePersist()
	 * @PreUpdate()
	 *
	 * @param Dish $dish
	 */
	public function preUpload(Dish $dish)
	{
		if (null !== $dish->getFile()) {
			$filename = sha1(uniqid(mt_rand(), true));
			$dish->setImage($filename . '.' . $dish->getFile()->guessExtension());
		}
	}

	/**
	 * @PostPersist()
	 * @PostUpdate()
	 *
	 * @param Dish $dish
	 */
	public function upload(Dish $dish)
	{
		if (null === $dish->getFile()) {
			return;
		}

		// if there is an error an exception will be thrown
		$dish->getFile()->move($this->dishesDirectory, $dish->getImage());

		// check if we have an old image
		if (!is_null($dish->getTemp())) {
			@unlink($this->dishesDirectory . '/' . $dish->getTemp());
			$dish->setTemp(null);
		}
		$dish->setFile(null);
	}

	/**
	 * @PostRemove()
	 *
	 * @param Dish $dish
	 */
	public function removeUpload(Dish $dish)
	{
		if ($file = $this->getAbsolutePath($dish)) {
			@unlink($file);
		}
	}

	/**
	 * @param Dish $dish
	 * @return null|string
	 */
	private function getAbsolutePath(Dish $dish)
	{
		return null === $dish->getImage()
			? null
			: $this->dishesDirectory . '/' . $dish->getImage();
	}
}