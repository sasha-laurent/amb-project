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
                
                $resource->setOwner($this->getUser());
                $em->persist($resource);
                $em->flush();

                $flashMessage = ($resource->getId()) ? 'Ressource ajoutée' : 'Ressource modifiée';
                $request->getSession()->getFlashBag()->add('success', $flashMessage);
            
            }
        }

        return $this->render('::Backend/form.html.twig', 
            array(
                'form' => $form->createView(),
                'mainTitle' => (($resource->getId()) ? 'Ajout d\'une ressource' : 'Modification d\'une ressource'),
            ));
    }

    public function newAction(Request $request)
    {
        $resource = new Resource();
        return $this->renderForm($resource);
    }

    public function editAction($id)
    {

    }

    public function deleteAction($id)
    {

    }
    
}
