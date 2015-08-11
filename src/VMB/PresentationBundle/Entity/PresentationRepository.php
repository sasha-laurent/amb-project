<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * PresentationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * Presentations par defaut sur une thematique coincent completement.
 */
class PresentationRepository extends EntityRepository
{
	public function getPresentations($page, $nbPerPage, $topic=null, $public=true, $official=true, $default='all', $user = null, $search = null)
	{
		$builder = $this->createQueryBuilder('p')
					  ->orderBy('p.dateUpdate', 'DESC');
		
		if($topic !== null) {
			$builder->join('p.topic', 't')
			->andWhere('t.lft >= :topicLft')->setParameter('topicLft', $topic->getLft())
			->andWhere('t.rgt <= :topicRgt')->setParameter('topicRgt', $topic->getRgt())
			->andWhere('t.root = :topicRoot')->setParameter('topicRoot', $topic->getRoot());
			//->andWhere('t.id = :tid')->setParameter('tid', $topic->getId());
		}
			  
		if(is_bool($public)) {
			$builder->andWhere('p.public = :public')
			->setParameter('public', $public);
		}
		
		if(is_bool($official)) {
			$builder->andWhere('p.official = :official')
			->setParameter('official', $official);
		}
		
		if(is_bool($default)) {
			$builder->andWhere('p.dfault = :default')
			->setParameter('default', $default);
		}
		
		if($user !== null) {
			$builder->andWhere('p.owner = :user')->setParameter('user', $user);
		}
		
		if($search !== null) {
			$builder->andWhere('p.title LIKE :keyword OR p.description LIKE :keyword')->setParameter('keyword', '%'.$search.'%');
		}
		//die($builder->getDQL());
		$query = $builder->getQuery();
		//$query->setFirstResult(($page-1) * $nbPerPage)
		//	  ->setMaxResults($nbPerPage);

		return new Paginator($query, true);
	}
	
	public function findWithSortedResources($id)
	{
		$qb = $this
			->createQueryBuilder('p')
			->leftJoin('p.resources', 'res')
			->addSelect('res')
			->orderBy('res.sort', 'ASC')
			->where('p.id = :id')
			->setParameter('id', $id)
		;

		return $qb
		->getQuery()
		->getSingleResult();
	}
	
	public function findWithAllMatrixResources($id)
	{
		$qb = $this
			->createQueryBuilder('p')
			->leftJoin('p.resources', 'checkedRes')->addSelect('checkedRes')
			->leftJoin('p.matrix', 'm')->addSelect('m')
			->leftJoin('m.levels', 'lvl')->addSelect('lvl')
			->leftJoin('m.povs', 'pov')->addSelect('pov')
			->leftJoin('m.resources', 'usedRes')->addSelect('usedRes')
			->leftJoin('usedRes.resource', 'res')->addSelect('res')
			->orderBy('checkedRes.sort', 'ASC')
			->addOrderBy('lvl.sort', 'ASC')
			->addOrderBy('pov.sort', 'ASC')
			->where('p.id = :id')
			->setParameter('id', $id)
		;

		return $qb
		->getQuery()
		->getSingleResult();
	}
	
	public function findWithConcreteResources($id)
	{
		$qb = $this
			->createQueryBuilder('p')
			->leftJoin('p.resources', 'checkedRes')
			->addSelect('checkedRes')
			->leftJoin('checkedRes.usedResource', 'usedRes')
			->addSelect('usedRes')
			->leftJoin('usedRes.resource', 'res')
			->addSelect('res')
			->leftJoin('p.annotations', 'a')
			->addSelect('a')
			->orderBy('checkedRes.sort', 'ASC')
			->addOrderBy('a.beginning', 'ASC')
			->where('p.id = :id')
			->setParameter('id', $id)
		;

		return $qb
		->getQuery()
		->getSingleResult();
	}
}
