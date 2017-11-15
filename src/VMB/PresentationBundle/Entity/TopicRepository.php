<?php

namespace VMB\PresentationBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * TopicRepository 
 */
class TopicRepository extends NestedTreeRepository
{
	/**
	 * Number of presentations linked to each topic.
	 * TODO: Join on presentations table, parametrized by "default", "official", "public/personal" args
	 ** $public=true, $official=true, $default='all', $user = null
	 */
	public function getPresentationsCounts($topic)
	{
		$count = 0;
		$query_res = 0;
		if($topic != null)
		{
			try{ // Calculate own presentations count
				$presentation_repo = $this->getEntityManager()->getRepository('VMBPresentationBundle:Presentation');
				$qb = $presentation_repo->createQueryBuilder('p')->select('count(p)')
				->where('p.topic = :tpc')->setParameter('tpc', $topic);
				$query_res = $qb->getQuery()->getSingleScalarResult();	
				$count += $query_res;

			} catch (\Doctrine\ORM\NonUniqueResultException $e)
			{
				echo $e;
			}

            // Calculate topic's children presentations count
			$children = $topic->getChildren();
            foreach($children as $c){
            	$query_res = $this->getPresentationsCounts($c);
            	$count += $query_res;
       		}
    	}
    	// Return total presentations count
		return $count;
	}
        
        /**
	 * Number of presentations linked to each topic which are visible for one user.
	 * TODO: Join on presentations table, parametrized by "default", "official", "public/personal" args
	 ** $public=true, $official=true, $default='all', $user = null
	 */
	public function getVisiblePresentationsCounts($topic,$user)
	{
		$count = 0;
        $total =0;
		$query_res = 0;
		if($topic != null)
		{
			try{ // Calculate own presentations count
				$presentation_repo = $this->getEntityManager()->getRepository('VMBPresentationBundle:Presentation');
				$qb = $presentation_repo->createQueryBuilder('p')->select('count(p)')
				->where('p.topic = :tpc')->setParameter('tpc', $topic)
                                ->andWhere('p.public = 1 OR p.owner = :user')->setParameter('user', $user);
				$query_res = $qb->getQuery()->getSingleScalarResult();	
				$count += $query_res;
                                $total+=$query_res;

			} catch (\Doctrine\ORM\NonUniqueResultException $e)
			{
				echo $e;
			}

            // Calculate topic's children presentations count
			$children = $topic->getChildren();
            foreach($children as $c){
            	$query_res = $this->getVisiblePresentationsCounts($c,$user)[0];
            	$count += $query_res;
       		}
    	}
    	// Return total presentations count
		return array($count,$total);
	}
        
        /**
	 * Number of presentations in a parent topic only
	 * TODO: Join on presentations table, parametrized by "default", "official", "public/personal" args
	 ** $public=true, $official=true, $default='all', $user = null
	 */
	public function getParentPresentationsCounts($topic,$user)
	{
		$count = 0;
		$query_res = 0;
		if($topic != null)
		{
			try{ // Calculate own presentations count
				$presentation_repo = $this->getEntityManager()->getRepository('VMBPresentationBundle:Presentation');
				$qb = $presentation_repo->createQueryBuilder('p')->select('count(p)')
				->where('p.topic = :tpc')->setParameter('tpc', $topic)
                                ->andWhere('p.public = 1 OR p.owner = :user')->setParameter('user', $user);
				$query_res = $qb->getQuery()->getSingleScalarResult();	
				$count += $query_res;

			} catch (\Doctrine\ORM\NonUniqueResultException $e)
			{
				echo $e;
			}
    	}
    	// Return total presentations count
		return $count;
	}
}