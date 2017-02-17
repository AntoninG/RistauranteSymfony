<?php

namespace DWBD\SecurityBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class StringToArrayUserTransformer
 * Allows to transform an array of roles into a string usable in a select tag
 * Uses only the first roles
 *
 * @package DWBD\SecurityBundle\Form\DataTransformer
 */
class StringToArrayUserTransformer implements DataTransformerInterface
{
	/**
	 * Transforms an array to a string.
	 * POSSIBLE LOSS OF DATA
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	public function transform($array)
	{
		return $array[0];
	}

	/**
	 * Transforms a string to an array.
	 *
	 * @param  string $string
	 *
	 * @return array
	 */
	public function reverseTransform($string)
	{
		return array($string);
	}
}
