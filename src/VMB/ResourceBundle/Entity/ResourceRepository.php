<?php

namespace VMB\ResourceBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ResourceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ResourceRepository extends EntityRepository
{
	public function getResources($page, $nbPerPage, $topic=null, $official=true, $user = null, $search = null)
	{
		$builder = $this->createQueryBuilder('r')
			->orderBy('r.type');
		
		if($topic != null) {
			$builder->join('r.topic', 't')
			->andWhere('t.lft >= :topicLft')->setParameter('topicLft', $topic->getLft())
			->andWhere('t.rgt <= :topicRgt')->setParameter('topicRgt', $topic->getRgt())
			->andWhere('t.root = :topicRoot')->setParameter('topicRoot', $topic->getRoot());
		}
		
		if(is_bool($official)) {
			$builder->andWhere('r.trusted = :official')
			->setParameter('official', $official);
		}
		
		if($user != null) {
			$builder->andWhere('r.owner = :user')->setParameter('user', $user);
		}
		
		if($search != null) {
			$builder->andWhere('r.title LIKE :keyword OR r.keywords LIKE :keyword OR r.description LIKE :keyword')->setParameter('keyword', '%'.$search.'%');
		}

		$query = $builder->getQuery();
		$query->setFirstResult(($page-1) * $nbPerPage)
			  ->setMaxResults($nbPerPage);

		return new Paginator($query, true);
	}
	
	
	public function findByTopicSortedByType($topic, $official =  true, $user = null, $exclusive = false)
	{
		$qb = $this
			->createQueryBuilder('r')
			->orderBy('r.type')
			->join('r.topic', 't')
			->andWhere('t.lft >= :topicLft')->setParameter('topicLft', $topic->getLft())
			->andWhere('t.rgt <= :topicRgt')->setParameter('topicRgt', $topic->getRgt())
			->andWhere('t.root = :topicRoot')->setParameter('topicRoot', $topic->getRoot())
		;
		
		if($official !== null) {
			$qb->andWhere('r.trusted = :official')->setParameter('official', $official);
		}
		
		if($user != null) {
			if($exclusive) {
				$qb->andWhere('r.owner <> :user')->setParameter('user', $user);
			}
			else {
				$qb->andWhere('r.owner = :user')->setParameter('user', $user);
			}
		}
		return $qb
		->getQuery()
		->getResult();
	}
	
	public function findByKeyword($keyword, $topicId = null)
	{
		$qb = $this
			->createQueryBuilder('r')
			->orderBy('r.type')
		;
		$qb->where('r.title LIKE :keyword')->setParameter('keyword', '%'.$keyword.'%');
		
		if($topicId !== null) {
			$qb->innerJoin('r.topic', 't')
			->where('t.topic = :topic')->andWhere('IDENTITY(t.id) = :topicId')->setParameter('topicId', $topicId);
		}

		return $qb
		->getQuery()
		->getResult();
	}
}
