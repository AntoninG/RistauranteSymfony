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
	 * 		id => translation
	 */
	public static function getCategoriesForForm()
	{
		return array(
			self::ENTREE 	=> 'Entree',
			self::DISH 		=> 'Main dish',
			self::DESSERT	=> 'Dessert',
			self::CHEESE_PLATE => 'Cheese plate',
			self::APPETIZER => 'Appetizer'
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