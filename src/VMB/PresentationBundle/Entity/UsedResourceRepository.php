<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UsedResourceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsedResourceRepository extends EntityRepository
{
	public function findByCoordinates($matrix, $pov, $level, $resource)
	{
		$qb = $this
			->createQueryBuilder('u')
			->where('IDENTITY(u.pov) = :pov')
			->andWhere('IDENTITY(u.level) = :level')
			->andWhere('IDENTITY(u.resource) = :resource')
			->andWhere('IDENTITY(u.matrix) = :matrix')
			->setParameter('pov', $pov)
			->setParameter('level', $level)
			->setParameter('matrix', $matrix)
			->setParameter('resource', $resource)
		;

		return $qb
		->getQuery()
		->getSingleResult();
	}
}
