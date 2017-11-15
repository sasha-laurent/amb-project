<?php

namespace VMB\QuizBundle\Evaluation;
use Symfony\Component\HttpFoundation\Request;    
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

    
class EvalListener {
        
    public function destroyEvalConstants(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        $request = $event->getRequest();
        $session = $request->getSession();
        
        if($request->isXmlHttpRequest() == false || $request->query->get('id')!=null)
        {
            $session->remove('idQuests');
        }
    }
}