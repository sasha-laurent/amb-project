<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class OntologyController extends Controller
{
	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function mainAction()
    {
        $ontology = $this->get('ontology');
        $ontologies = $ontology->getOntologies();
        
        $resources = $this->getDoctrine()->getManager()->getRepository('VMBResourceBundle:Resource')->findAll();

        return $this->render('VMBPresentationBundle:Ontology:main.html.twig',
            array('videos' => $resources,
                'ontologies' => $ontologies));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function indexationpageAction()
    {
        $ontology = $this->get('ontology');
        $ontologies = $ontology->getOntologies();
        
        $videos = $this->getDoctrine()->getManager()->getRepository('VMBResourceBundle:Resource')->findAll();

        return $this->render('VMBPresentationBundle:Ontology:indexationpage.html.twig',
            array('videos' => $videos,
                'ontologies' => $ontologies)); 
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function editAction(Request $request)
    {
        $ontology = $this->get('ontology');
        $id = $request->query->get('ontology');
        $ontArr = $ontology->getOntology($id);
        $name = 'new ontology';
        $file = 'ontology.json';
        if (is_null($ontArr)) { // new ontologie
            $id = '0';
            $name = 'New ontology';
        }
        else { // edit ontologie
            $name = $ontArr['name'];
            $file = $ontology->getOntologyFile($id);
        }
        return $this->render('VMBPresentationBundle:Ontology:edit.html.twig', array(
            'name' => $name,
            'file' => $file,
            'id'   => $id
            ));
    }

    /*public function createAction(Request $request)
    {
        $ontology = $this->get('ontology');
        $id = $request->query->get('ontology');
        $ontArr = $ontology->getOntology($id);
        $name = $request->get('name');
        $file = 'ontology.json';
        $file = $ontology->getOntologyFile($id);
        return $this->render('VMBPresentationBundle:Ontology:edit.html.twig', array(
            'name' => $name,
            'file' => $file,
            'id'   => $id
            ));
    }*/

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function indexAction(Request $request)
    {
    	$video = $request->query->get('video');
        $ontology = $this->get('ontology');
        $id = $request->query->get('ontology');
        $ontArr = $ontology->getOntology($id);

    	if (count($video) == 0 || is_null($ontArr) ) {
    		return $this->redirect($this->generateUrl('ontology_main'));
    	}

        return $this->render('VMBPresentationBundle:Ontology:indexation.html.twig', array(
        	'videoID' => $video,
            'ontologieName' => $ontArr['name'],
            'indexFile' => $ontology->getIndexFile($id),
            'ontologyFile' => $ontology->getOntologyFile($id),
            'id'           => $id
        ));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function searchAction ($i) 
    {
        $request  = $this->getRequest();
        $ont = $request->query->get('ontology');
        if (isset($ont) && !empty($ont) ) {
            return $this->redirect($this->generateUrl('search', array('i' => $ont) ) );
        }

        $ontology = $this->get('ontology');
        $ontologies = $ontology->getOntologies();

        $index = "index".$i.".json";
        $ontology = "ontology".$i.".json";
        return $this->render('TelecomVmbBundle:Admin:search.html.twig', array(
            'indexFile' => $index,
            'ontologyFile' => $ontology,
            'value' => $i,
            'ontologies' => $ontologies));
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function saveOntologyAction(Request $request)
    {
        $ontology = $this->get('ontology');
    	$data = $request->request->get('data');
        $id = $request->request->get('id');
        
        $ontArr = $ontology->getOntology($id);
        if(is_null($ontArr)) { // create new ontologie
            $id = $ontology->newOntology();
        }

    	$file = $ontology->getAbsoluteOntologyFile($id);
    	file_put_contents($file, $data);
    	return new Response('ok');
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function saveIndexAction(Request $request)
    {
        $ontology = $this->get('ontology');
    	$data = $request->request->get('data');
        $id = $request->request->get('id');
    	$file = $ontology->getAbsoluteIndexFile($id);
    	file_put_contents($file, $data);
    	return new Response('ok');
    }

}
