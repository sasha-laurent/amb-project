<?php

namespace VMB\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\UserBundle\Entity\User;

class CaddyController extends Controller
{
	
	public function addPresentationAction()
	{
		$request = $this->container->get('request');
		$id = $this->container->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
			
			$presentation = $em
				->getRepository('VMBPresentationBundle:Presentation')
				->find($id);
			
			if(!$presentation){
				throw $this->createNotFoundException(
				'Aucune présentation n\'a pu être trouvé'
				);
			}
			$user->addPresentation($presentation);
			$em->flush();
			return new Response('ok');
		}
		return new Response('failure');
	}
	
	public function removePresentationAction()
	{
		$request = $this->container->get('request');
		$id = $this->container->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
		
			$presentation = $em
				->getRepository('VMBPresentationBundle:Presentation')
				->find($id);
		
			if(!$presentation){
				throw $this->createNotFoundException(
				'Aucune présentation n\'a pu être trouvé'
				);
			}
			$user->removePresentation($presentation);
			$em->flush();
			return new Response('ok');
		}
		return new Response('failure');
	}
	
	public function addResourceAction()
	{
		$request = $this->container->get('request');
		$id = $this->container->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
		
			$resource = $em
				->getRepository('VMBResourceBundle:Resource')
				->find($id);
		
			if(!$resource){
				throw $this->createNotFoundException(
				'Aucune présentation n\'a pu être trouvé'
				);
			}
			$user->addResource($resource);
			$em->flush();
			return new Response('ok');
		}
		return new Response('failure');
	}
	
	public function removeResourceAction()
	{
		$request = $this->container->get('request');
		$id = $this->container->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
		
			$resource = $em
				->getRepository('VMBResourceBundle:Resource')
				->find($id);
		
			if(!$resource){
				throw $this->createNotFoundException(
				'Aucune présentation n\'a pu être trouvé'
				);
			}
			$user->removeResource($resource);
			$em->flush();
			return new Response('ok');
		}
		return new Response('failure');
	}
}
