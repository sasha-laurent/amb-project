<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Component\Validator\Constraints as Assert;  
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Form;

use VMB\ResourceBundle\Entity\Resource;
use VMB\ResourceBundle\Form\ResourceType;

class UploadController extends Controller
{
    
    protected function renderForm($resource)
    {
        $request = $this->get('request');
        
        $form = $this
            ->get('form.factory')
            ->create(new ResourceType(), $resource);
            
        if ($request->isMethod('POST')) 
        {
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                $em = $this->getDoctrine()->getManager();
                
                $em->persist($resource);
                $em->flush();

                $flashMessage = ($resource->getId() == null) ? 'Ressource ajoutée' : 'Ressource modifiée';
                $request->getSession()->getFlashBag()->add('success', $flashMessage);
            
				return $this->redirect($this->generateUrl('resource'));
            }
        }

        return $this->render('::Backend/form.html.twig', 
            array(
                'form' => $form->createView(),
                'mainTitle' => (($resource->getId() == null) ? 'Ajout d\'une ressource' : 'Modification d\'une ressource'),
                'backButtonUrl' => $this->container->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('resource'))
            ));
    }

    public function newAction(Request $request)
    {
        $resource = new Resource();
        $resource->setOwner($this->getUser());
        return $this->renderForm($resource);
    }

    public function editAction($id)
    {
		$resource = $this->getResource($id);

		return $this->renderForm($resource);
    }    
    
    /**
     * Retrieve an existing Resource entity.
     */
    protected function getResource($id)
    {
        $resource = $this->getDoctrine()->getManager()->getRepository('VMBResourceBundle:Resource')->find($id);

		if ($resource == null) {
			throw $this->createNotFoundException('Unable to find Resource entity.');
		}

		return $resource;
    }
}
