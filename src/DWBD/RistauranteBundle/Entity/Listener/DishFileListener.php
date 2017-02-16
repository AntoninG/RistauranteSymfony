<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
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
	 * @ORM\PrePersist()
	 * @ORM\PreUpdate()
	 *
	 * @param Dish $dish
	 * @param LifecycleEventArgs $args
	 */
	public function preUpload(Dish $dish, LifecycleEventArgs $args)
	{
		if (null !== $dish->getFile()) {
			$filename = sha1(uniqid(mt_rand(), true));
			$dish->setImage($filename . '.' . $dish->getFile()->guessExtension());
		}
	}

	/**
	 * @ORM\PostPersist()
	 * @ORM\PostUpdate()
	 *
	 * @param Dish $dish
	 * @param LifecycleEventArgs $args
	 */
	public function upload(Dish $dish, LifecycleEventArgs $args)
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
	 * @ORM\PostRemove()
	 *
	 * @param Dish $dish
	 * @param LifecycleEventArgs $args
	 */
	public function removeUpload(Dish $dish, LifecycleEventArgs $args)
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