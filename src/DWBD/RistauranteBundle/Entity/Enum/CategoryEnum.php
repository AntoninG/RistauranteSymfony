<?php

namespace DWBD\RistauranteBundle\Entity\Enum;

/**
 * Class CategoryEnum
 * Represents a category of dish
 *
 * @package DWBD\RistauranteBundle\Entity\Enum
 */
abstract class CategoryEnum
{
	CONST ENTREE = 1;
	CONST DISH = 2;
	CONST DESSERT = 3;
	CONST CHEESE_PLATE = 4;
	CONST APPETIZER = 5;

	/**
	 * Returns an array used in forms
	 *
	 * @return array
	 *        id => translation
	 */
	public static function getCategoriesForForm()
	{
		return array(
			'Entree' => self::ENTREE,
			'Main dish' => self::DISH,
			'Dessert' => self::DESSERT,
			'Cheese plate' => self::CHEESE_PLATE,
			'Appetizer' => self::APPETIZER
		);
	}

	/**
	 * Returns and array with all categories
	 *
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

	/**
	 * Returns an array used to translate categories
	 *
	 * @return array
	 *        id => translation
	 */
	public static function getCategoriesTranslation()
	{
		return array(
			self::ENTREE => 'Entree',
			self::DISH => 'Main dish',
			self::DESSERT => 'Dessert',
			self::CHEESE_PLATE => 'Cheese plate',
			self::APPETIZER => 'Appetizer'
		);
	}
}
