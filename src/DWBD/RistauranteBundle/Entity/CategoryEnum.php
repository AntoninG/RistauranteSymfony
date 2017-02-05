<?php

namespace DWBD\RistauranteBundle\Entity;

abstract class CategoryEnum
{
	CONST ENTREE = 1;
	CONST DISH = 2;
	CONST DESSERT = 3;
	CONST CHEESE_PLATE = 4;
	CONST APPETIZER = 5;

	/**
	 * @return array
	 *        id => translation
	 */
	public static function getCategoriesForForm()
	{
		return array(
			'Entree' 	=> self::ENTREE,
			'Main dish' => self::DISH,
			'Dessert' 	=> self::DESSERT,
			'Cheese plate' => self::CHEESE_PLATE,
			'Appetizer' => self::APPETIZER
		);
	}

	/**
	 * @return array
	 */
	public static function getCategories()
	{
		return array(
			self::ENTREE,
			self::DISH,
			self::DESSERT,
			self::CHEESE_PLATE,
			self::APPETIZER
		);
	}
}