<?php

namespace VMB\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\UserBundle\Entity\User;

class CaddyController extends Controller
{
	
	/**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
	public function showAction($display)
	{
		return $this->render('VMBUserBundle:Caddy:show.html.twig', array(
            'mainTitle' => 'Caddy',
            'display' => $display
        ));
	} 
	
	/**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
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
	
	/**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
	public function removePresentationAction()
	{
		$request = $this->container->get('request');
		$id = $request->request->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest() || $request->getMethod() == 'POST'){
			$em = $this->getDoctrine()->getManager();
		
			$presentation = $em
				->getRepository('VMBPresentationBundle:Presentation')
				->find($id);
		
			if(!$presentation){
				return new Response('unknown');
			}
			$user->removePresentation($presentation);
			$em->flush();
			// Generate the response
			if($request->isXmlHttpRequest()) {
				return new Response('ok');
			}
			else {
				return $this->redirect($this->generateUrl('caddy_show_presentation'));
			}
		}
		return new Response('failure');
	}
	
	/**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
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
	
	/**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
	public function removeResourceAction()
	{
		$request = $this->container->get('request');
		$id = $request->request->get('id');
		$user = $this->getUser();
		if($request->isXmlHttpRequest() || $request->getMethod() == 'POST'){
			$em = $this->getDoctrine()->getManager();
		
			$resource = $em
				->getRepository('VMBResourceBundle:Resource')
				->find($id);
		
			if(!$resource){
				return new Response('unknown');
			}
			$user->removeResource($resource);
			$em->flush();
			
			if($request->isXmlHttpRequest()) {
				return new Response('ok');
			}
			else {
				return $this->redirect($this->generateUrl('caddy_show_resource'));
			}
		}
		return new Response('failure');
	}
}
