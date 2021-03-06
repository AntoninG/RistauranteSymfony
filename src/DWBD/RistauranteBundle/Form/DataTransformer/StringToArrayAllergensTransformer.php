<?php

namespace DWBD\RistauranteBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StringToArrayAllergensTransformer
 * Allow to transform an array of allergens into a string : each allergen is separated by a line return
 * 
 * @package DWBD\RistauranteBundle\Form\DataTransformer
 */
class StringToArrayAllergensTransformer implements DataTransformerInterface
{
	/**
	 * Transforms an array to a string.
	 *
	 * @param array $array
	 *
	 * @return string
	 *        each element separated by PHP_EOL
	 */
	public function transform($array)
	{
		if (!is_array($array)) return "";

		return implode(PHP_EOL, $array);
	}

	/**
	 * Transforms a string to an array
	 *
	 * @param string $string
	 *        one line == one value of the ouput array
	 *
	 * @return array
	 *        trim applied on each elements
	 */
	public function reverseTransform($string)
	{
		if (!is_string($string)) return array();

		$array = explode(PHP_EOL, $string);
		$array = array_filter($array, "trim");
		$array = array_map(function($element) {
			if (!empty($element)) return $element;
		}, $array);

		return $array;
	}

}
