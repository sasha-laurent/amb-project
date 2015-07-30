<?php
// src\VMB\PresentationBundle\Entity\TopicListener.php
namespace VMB\PresentationBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use VMB\PresentationBundle\Entity\Topic;

class TopicListener
{
	/**
	** Calculates an initial number of presentations linked to a Topic
	** after a topic has been loaded or refreshed if the count is default null value.
	** @param <Topic> t
	** @return <void> - information is persisted to the DB
	*/
	public function postLoad(Topic $t, LifecycleEventArgs $args)
	{
		$em = $args->getEntityManager();
		$pres_count = $t->getTotalIncludedPresentations();
		if(null === $pres_count)
		{
			$new_count = $em->getRepository('VMBPresentationBundle:Topic')->getPresentationsCounts($t);
			$t->setTotalIncludedPresentations($new_count);
			$em->persist($t);
			$em->flush();
		}
	}
}