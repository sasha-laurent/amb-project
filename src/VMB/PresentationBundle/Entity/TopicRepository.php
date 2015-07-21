<?php

namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TopicRepository
 */
class TopicRepository extends EntityRepository
{

	/**
	 * Number of presentations linked to each topic.
	 * TODO: Join on presentations table, parametrized by "default", "official", "public/personal" args
	 */
	public function findWithPresentationCounts($topic, $public=true, $official=true, $default='all', $user = null)
	{
		$count = 0;

		$qb = $this
		->createQueryBuilder('t')
		->join('t.presentation', 'p')->addSelect('count(p)')
		->andWhere('p.topic = :tpc')->setParameter('tpc', $topic);
		$count = $qb->getQuery()->getSingleScalarResult();

		$children = $topic->getChildren();
		if($children != null){
            foreach($children as $c){
            	$count += $this->findPresentationsCount($c);
       		}
    	}

		return $count;
	}
}