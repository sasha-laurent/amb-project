<?php
// src\VMB\PresentationBundle\Entity\PresentationListener.php
namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use VMB\PresentationBundle\Entity\Presentation;

/*
** Keeps the number of presentations in each topic update when
** a new presentation is persisted to or removed from the 
** database.
** See TopicListener/TopicRepository for the initialization of those counts.
*/
class PresentationListener
{
	
	public function postPersist(Presentation $p, LifecycleEventArgs $event)
	{
		$this->updateCountsBy($p, $event, 1);
	}

	public function preRemove(Presentation $p, LifecycleEventArgs $event)
	{
		$this->updateCountsBy($p, $event, -1);
	}

	public function updateCountsBy(Presentation $p, LifecycleEventArgs $event, $val)
	{
		$em = $event->getEntityManager();
		$p_topic = $p->getTopic();
		// Increment counts;
		$count = $p_topic->getTotalIncludedPresentations();
		$new_val = $count + $val;
		if($new_val >= 0)
		{
			$p_topic->setTotalIncludedPresentations($new_val);
			// Persist to DB
			$em->persist($p_topic);
			$em->flush();
		}
		$parent_t = $p_topic->getParent();
		// Propagate upwards
		while($parent_t != null)
		{
			$c = $parent_t->getTotalIncludedPresentations();
			$new_val = $c + $val;
			if($new_val >= 0)
			{
				$parent_t->setTotalIncludedPresentations($new_val);
				$em->persist($parent_t);
				$em->flush();
			}
			$parent_t = $parent_t->getParent();
		}
	}

}