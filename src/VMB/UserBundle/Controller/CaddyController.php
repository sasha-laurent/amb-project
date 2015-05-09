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
		$id = $request->request->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
			
			$presentation = $em
				->getRepository('VMBPresentationBundle:Presentation')
				->find($id);
			
			if(!$presentation){
				return new Response('unknown');
			}
			try {
				$user->addPresentation($presentation);
				$em->flush();
			}
			catch(\Exception $e) {
				return new Response('duplicate');
			}
			return new Response('ok');
		}
		return new Response('failure');
	}
	
	public function removePresentationAction()
	{
		$request = $this->container->get('request');
		$id = $request->request->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
		
			$presentation = $em
				->getRepository('VMBPresentationBundle:Presentation')
				->find($id);
		
			if(!$presentation){
				return new Response('unknown');
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
		$id = $request->request->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
		
			$resource = $em
				->getRepository('VMBResourceBundle:Resource')
				->find($id);
		
			if(!$resource){
				return new Response('unknown');
			}
			try {
				$user->addResource($resource);
				$em->flush();
				return new Response('ok');
			}
			catch(\Exception $e) {
				return new Response('duplicate');
			}
		}
		return new Response('failure');
	}
	
	public function removeResourceAction()
	{
		$request = $this->container->get('request');
		$id = $request->request->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest()){
			$em = $this->getDoctrine()->getManager();
		
			$resource = $em
				->getRepository('VMBResourceBundle:Resource')
				->find($id);
		
			if(!$resource){
				return new Response('unknown');
			}
			$user->removeResource($resource);
			$em->flush();
			return new Response('ok');
		}
		return new Response('failure');
	}
}
