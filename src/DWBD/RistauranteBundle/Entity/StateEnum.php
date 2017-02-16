<?php

namespace DWBD\RistauranteBundle\Entity;

abstract class StateEnum
{
	CONST STATE_DRAFT = 1;
	CONST STATE_WAITING = 2;
	CONST STATE_REFUSED = 3;
	CONST STATE_VALIDATED = 4;

	/**
	 * @return array
	 * 		id => translation
	 */
	public static function getStatesForForm()
	{
		return array(
			'Draft'		=> self::STATE_DRAFT,
			'Waiting for validation' => self::STATE_WAITING,
			'Refused' 	=> self::STATE_REFUSED,
			'Validated' => self::STATE_VALIDATED
		);
	}

	/**
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
