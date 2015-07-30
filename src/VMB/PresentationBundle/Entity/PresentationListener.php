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
		$p_topic = $p->getTopic();
		if(null === $p_topic){
			return;
		} else {
			$this->updateCountsBy($p_topic, $event, 1);
		}
	}

	public function postRemove(Presentation $p, LifecycleEventArgs $event)
	{
		$p_topic = $p->getTopic();
		if(null === $p_topic){
			return;
		} else {
			$this->updateCountsBy($p_topic, $event, -1);
		}
	}

	public function updateCountsBy(Topic $t = null, LifecycleEventArgs $event, $val)
	{
		if(null === $t){ // Stop when (parent) topic is not defined any more
			return;
		}
		$own_count = $t->getTotalIncludedPresentations();
		if($own_count + $val >= 0)
		{
			$t->setTotalIncludedPresentations($own_count + $val); // Increment/Decrement counts
			$em = $event->getEntityManager();
			$em->persist($t); // Persist to DB
			$em->flush();
		}
		$parent_t = $t->getParent();
		$this->updateCountsBy($parent_t, $event, $val);  // Propagate upwards
	}

}