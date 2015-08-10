<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\PresentationBundle\Entity\Ontology;
use VMB\PresentationBundle\Form\OntologyType;

class OntologyController extends Controller
{
	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function mainAction()
    {
		$em = $this->getDoctrine()->getManager();
        $ontologies = $em->getRepository('VMBPresentationBundle:Ontology')->findAll();

        return $this->render('VMBPresentationBundle:Ontology:main.html.twig',
            array(
				'mainTitle' => $this->get('translator')->trans('menu.ontology'),
				'addButtonUrl' => $this->generateUrl('ontology_edit'),
                'ontologies' => $ontologies
            ));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function indexationpageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ontologies = $em->getRepository('VMBPresentationBundle:Ontology')->findAll();
        $videos = $em->getRepository('VMBResourceBundle:Resource')->findAll();

        return $this->render('VMBPresentationBundle:Ontology:indexationpage.html.twig',
            array('videos' => $videos,
                'ontologies' => $ontologies)); 
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function editAction(Request $request)
    {
        $id = $request->query->get('ontology');
        $ontology = null;
        
        if($id != null) {
			$em = $this->getDoctrine()->getManager();
			$ontology = $em->getRepository('VMBPresentationBundle:Ontology')->find($id);
        }
        
        $name = 'new ontology';
        $file = 'ontology.json';
        if (is_null($ontology)) { // new ontologie
            $ontology = new Ontology();
            $name = 'New ontology';
            $ontology->setName($name);
            $id = '0';
        }
        else { // edit ontologie
            $name = $ontology->getName();
            $file = $ontology->getOntologyFile($id);
        }
        
        $form = $this
			->get('form.factory')
			->create(new OntologyType(), $ontology);
        
        return $this->render('VMBPresentationBundle:Ontology:edit.html.twig', array(
            'name' => $name,
            'file' => $file,
            'id'   => $id,
            'form' => $form->createView(),
            'backButtonUrl' => $this->generateUrl('ontology_main'),
            'mainTitle' => $this->get('translator')->trans('ontology.edit')
            ));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function indexAction(Request $request)
    {
    	$video = $request->query->get('video');
        $id = $request->query->get('ontology');
        $em = $this->getDoctrine()->getManager();
        $ontology = $em->getRepository('VMBPresentationBundle:Ontology')->find($id);

    	if (count($video) == 0 || is_null($ontology) ) {
    		return $this->redirect($this->generateUrl('ontology_main'));
    	}

        return $this->render('VMBPresentationBundle:Ontology:indexation.html.twig', array(
        	'videoID' => $video,
            'ontologieName' => $ontology->getName(),
            'indexFile' => $ontology->getIndexFile($id),
            'ontologyFile' => $ontology->getOntologyFile($id),
            'id'           => $id,
            'backButtonUrl' => $this->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('vmb_resource_browse')),
            'mainTitle' => $this->get('translator')->trans('ontology.create_index')
        ));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function searchAction ($i) 
    {
		$em = $this->getDoctrine()->getManager();
		$ontology = $em->getRepository('VMBPresentationBundle:Ontology')->find($i);

		$index = "index".$i.".json";
		$ontologyFile = "ontology".$i.".json";
		return $this->render('VMBPresentationBundle:Ontology:search.html.twig', array(
			'indexFile' => $index,
			'ontologyFile' => $ontologyFile,
			'value' => $i,
			'ontology' => $ontology));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function saveOntologyAction(Request $request)
    {
		$isNew = false;
    	$data = $request->request->get('data');
        $id = $request->request->get('id');
        
        $em = $this->getDoctrine()->getManager();
        $ontology = $em->getRepository('VMBPresentationBundle:Ontology')->find($id);
        if(is_null($ontology)) { // create new ontologie
            $ontology = new Ontology();
            $isNew = true;
        }
        
        $request = $this->get('request');
			
		if ($request->isMethod('POST')) 
		{
			$ontology->setName($request->request->get('title'));
			if($isNew) {
				$topic = $em->getRepository('VMBPresentationBundle:Topic')->find($request->request->get('topic'));
				if($topic != null) {
					$ontology->setTopic($topic);
					$em->persist($ontology);
					$em->flush();
					
					// We create the corresponding files
					file_put_contents($this->getAbsoluteIndexFile($ontology->getId()), '{}');
				}
				else {
					return new Response('ok');
				}
			}
			else {
				$em->flush();
			}
			file_put_contents($this->getAbsoluteOntologyFile($ontology->getId()), $data);
		}
    	return new Response($ontology->getId());
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function saveIndexAction(Request $request)
    {
    	$data = $request->request->get('data');
        $id = $request->request->get('id');
        $resourcesId = $request->request->get('videoID');
        
        $em = $this->getDoctrine()->getManager();
        foreach($resourcesId as $resId) {
			$resource = $em->getRepository('VMBResourceBundle:Resource')->find($resId);
			if($resource != null) {
				$resource->setIndexed(true);
				// Since the user is a teacher
				$resource->setTrusted(true);
			}
		}
		$em->flush();
		$file = $this->getAbsoluteIndexFile($id);
		file_put_contents($file, $data);
		return new Response('ok');
    }
    
    /**
     * Deletes an Ontology entity.
     *
     */
    /**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function deleteAction(Request $request, $id)
    {
        $ontology = $this->getOntology($id);
	
		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($ontology);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Structure supprimée');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('ontology_main'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'la structure des connaissances "'.$ontology->getName().'"',
			'mainTitle' => 'Suppression d\'une structure',
			'backButtonUrl' => $this->generateUrl('ontology_main')
		));

    }
    
    public function listAvailable($resourceId)
    {
		return new Response('nope');
	}
    
    /**
     * Retrieve an existing Ontology entity.
     */
    protected function getOntology($id)
    {
        $ontology = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Ontology')->find($id);

		if ($ontology == null) {
			throw $this->createNotFoundException('Unable to find Ontology entity.');
		}

		return $ontology;
    }
    
    public function getAbsoluteIndexFile($id) {
        return $this->get('kernel')->getRootDir().'/../web/bundles/telecomvmb/json/index'.$id.'.json';
    }

    public function getAbsoluteOntologyFile($id) {
        return $this->get('kernel')->getRootDir().'/../web/bundles/telecomvmb/json/ontology'.$id.'.json';
    }

}
