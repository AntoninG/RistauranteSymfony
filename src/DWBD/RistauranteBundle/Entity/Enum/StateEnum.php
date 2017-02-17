<?php

namespace DWBD\RistauranteBundle\Entity\Enum;

/**
 * Class StateEnum
 * Represents a state of a menu, dish or reservation
 *
 * @package DWBD\RistauranteBundle\Entity\Enum
 */
abstract class StateEnum
{
	CONST STATE_DRAFT = 1;
	CONST STATE_WAITING = 2;
	CONST STATE_REFUSED = 3;
	CONST STATE_VALIDATED = 4;

	/**
	 * Returns an array used in forms
	 *
	 * @return array
	 *        translation => id
	 */
	public static function getStatesForForm()
	{
		return array(
			'Draft' => self::STATE_DRAFT,
			'Waiting for validation' => self::STATE_WAITING,
			'Refused' => self::STATE_REFUSED,
			'Validated' => self::STATE_VALIDATED
		);
	}

	/**
	 * Returns and array with all states
	 *
	 * @return array
	 */
	public static function getStates()
	{
		return array(
			self::STATE_DRAFT,
			self::STATE_WAITING,
			self::STATE_REFUSED,
			self::STATE_VALIDATED
		);
	}

	/**
	 * Returns an array used to translate states
	 *
	 * @return array
	 *        id => translation
	 */
	public static function getStatesTranslation()
	{
		return array(
			self::STATE_DRAFT => 'Draft',
			self::STATE_WAITING => 'Waiting for validation',
			self::STATE_REFUSED => 'Refused',
			self::STATE_VALIDATED => 'Validated'
		);
	}
}
