<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Component\Validator\Constraints as Assert;  
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Form;

use VMB\ResourceBundle\Entity\Resource;

class UploadController extends Controller
{
    
 /*
 *
 *Création du formulaire d'importation des ressources
 *
 */
        public function formAction(Request $request)
        {
             $resource = new Resource();
             $form = $this->createFormBuilder($resource)
                ->add('title')
                ->add('description')
                ->add('type')
                ->add('filename')
                ->add('file')
                ->getForm();
            
             $request = $this->getRequest();
            
        /*Traitement de la requête
        *
        *Gestion du retour de formulaire
        *
        */
            
        if ($request->isMethod('POST'))
        {   

            $form->handleRequest($request);
            if ($form->isValid())
            {
                $em = $this->getDoctrine()->getManager();
                
                // On récupère l'extension du fichier importé
                $ext = $resource->getExtension();
                // On récupère le nom du fichier
                $name = $resource->getTitle();
                $resource->preUpload();
                

               //$path = $resource->getFilePath();
            
                
                $em->persist($resource);
                $em->flush();

                 return $this->render('VMBResourceBundle:Upload:index.html.twig', array(
            'form' => $form->createView(),
        ));
            }
        }
            return $this->render('VMBResourceBundle:Upload:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
}
