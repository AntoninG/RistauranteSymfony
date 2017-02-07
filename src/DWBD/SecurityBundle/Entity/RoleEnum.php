<?php

namespace DWBD\SecurityBundle\Entity;

/**
 * Class RoleEnum
 * Enumeration of all roles accepted for users
 *
 * @package DWBD\SecurityBundle\Entity
 */
class RoleEnum
{
	CONST USER   = "ROLE_USER";
	CONST WAITER = "ROLE_WAITER";
	CONST EDITOR = "ROLE_EDITOR";
	CONST REVIEWER = "ROLE_REVIEWER";
	CONST CHIEF  = "ROLE_CHIEF";
	CONST ADMIN  = "ROLE_ADMIN";

	public static function getRolesForForm()
	{
		return array(
			self::USER 	 => self::USER,
			self::WAITER => self::WAITER,
			self::EDITOR => self::EDITOR,
			self::REVIEWER => self::REVIEWER,
			self::CHIEF  => self::CHIEF,
			self::ADMIN  => self::ADMIN
		);
	}

	public static function getRoles()
	{
		return array(
			self::USER,
			self::WAITER,
			self::EDITOR,
			self::REVIEWER,
			self::CHIEF,
			self::ADMIN
		);
	}
}