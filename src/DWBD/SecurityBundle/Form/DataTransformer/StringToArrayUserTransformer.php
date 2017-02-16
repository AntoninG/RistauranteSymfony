<?php

namespace DWBD\SecurityBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

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
