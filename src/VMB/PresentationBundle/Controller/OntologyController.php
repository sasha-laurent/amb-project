<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\PresentationBundle\Entity\Ontology;

class OntologyController extends Controller
{
	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function mainAction()
    {
		$em = $this->getDoctrine()->getManager();
        $ontologies = $em->getRepository('VMBPresentationBundle:Ontology')->findAll();
        $resources = $em->getRepository('VMBResourceBundle:Resource')->findAll();

        return $this->render('VMBPresentationBundle:Ontology:main.html.twig',
            array('videos' => $resources,
                'ontologies' => $ontologies));
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
            $id = '0';
            $name = 'New ontology';
        }
        else { // edit ontologie
            $name = $ontology->getName();
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

        $em = $this->getDoctrine()->getManager();
        $ontologies = $em->getRepository('VMBPresentationBundle:Ontology')->findAll();

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
    	$data = $request->request->get('data');
        $id = $request->request->get('id');
        
        $ontology = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Ontology')->find($id);
        if(is_null($ontology)) { // create new ontologie
            $ontology = $this->newOntology();
        }

    	$file = $this->getAbsoluteOntologyFile($ontology->getId());
    	file_put_contents($file, $data);
    	return new Response('ok');
    }

	/**
    * @Security("has_role('ROLE_TEACHER')")
    */
    public function saveIndexAction(Request $request)
    {
    	$data = $request->request->get('data');
        $id = $request->request->get('id');
    	$file = $this->getAbsoluteIndexFile($id);
    	file_put_contents($file, $data);
    	return new Response('ok');
    }
    
    public function newOntology() {
		$em = $this->getDoctrine()->getManager();
		
		// We create the new entity
		$ontology = new Ontology();
		$ontology->setName('New ontology');
		$em->persist($ontology);
        
        // We flush to get and id
        $em->flush();
        
        // We create the corresponding files
        file_put_contents($this->getAbsoluteIndexFile($ontology->getId()), '{}');
        file_put_contents($this->getAbsoluteOntologyFile($ontology->getId()), '[]');
        
        return $ontology;
    }
    public function getAbsoluteIndexFile($id) {
        return $this->get('kernel')->getRootDir().'/../web/bundles/telecomvmb/json/index'.$id.'.json';
    }

    public function getAbsoluteOntologyFile($id) {
        return $this->get('kernel')->getRootDir().'/../web/bundles/telecomvmb/json/ontology'.$id.'.json';
    }

}
