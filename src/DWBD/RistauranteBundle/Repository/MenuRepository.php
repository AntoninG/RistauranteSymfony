<?php

namespace DWBD\RistauranteBundle\Repository;

/**
 * MenuRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MenuRepository extends \Doctrine\ORM\EntityRepository
{
	public function getMenusWithoutDishes()
	{
		$query = $this->_em->createQuery("
		SELECT me FROM DWBD\RistauranteBundle\Entity\Menu me WHERE me.id NOT IN (
			SELECT m.id FROM DWBD\RistauranteBundle\Entity\Menu m INNER JOIN m.dishes d
		)
		");

		return $query->getResult();
	}
}