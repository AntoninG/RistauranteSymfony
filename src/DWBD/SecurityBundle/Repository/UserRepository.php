<?php

namespace DWBD\SecurityBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
	public function loadUserByUsername($username)
	{
		return $this->createQueryBuilder('u')
			->where('u.username = :username OR u.email = :email')
			->setParameter('username', $username)
			->setParameter('email', $username)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * @param array|string $roles
	 *
	 * @return  array
	 */
	public function findByRoles($roles)
	{
		if (!is_string($roles) && !is_array($roles)) {
			throw new \InvalidArgumentException('$roles must be an array or a string');
		}

		$qb = $this->createQueryBuilder('u');
		if (is_string($roles)) {
			$qb->where($qb->expr()->like('u.roles', ':roles'))->setParameter('roles', '%"' . $roles . '"%');
		} else {
			foreach ($roles as $role) {
				$qb->orWhere($qb->expr()->like('u.roles', ':roles'))->setParameter('roles', '%"' . $role . '"%');
			}
		}

		return $qb->getQuery()->getResult();
	}
}
