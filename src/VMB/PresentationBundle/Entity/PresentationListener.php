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

	public function postRemove(Presentation $p, LifecycleEventArgs $event)
	{
		$this->updateCountsBy($p, $event, -1);
	}

	public function updateCountsBy(Presentation $p = null, LifecycleEventArgs $event, $val)
	{
		if(null === $p){ // Stop when parent is not defined
			return;
		}
		$em = $event->getEntityManager();
		$p_topic = $p->getTopic();
		$own_count = $p_topic->getTotalIncludedPresentations();
		if($own_count + $val >= 0)
		{
			$p_topic->setTotalIncludedPresentations($own_count + $val); // Increment/Decrement counts
			$em->persist($p_topic); // Persist to DB
			$em->flush();
		}
		$parent_t = $p_topic->getParent();
		// Propagate upwards
		$this->updateCountsBy($parent_t, $event, $val);
	}

}