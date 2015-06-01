<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;

use VMB\PresentationBundle\Entity\Topic;
use VMB\PresentationBundle\Form\TopicType;

/**
 * Topic controller.
 *
 */
class TopicController extends Controller
{
	
    /**
     * Lists all Topic entities.
     *
     */
    public function indexAction()
    {
		$em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('VMBPresentationBundle:Topic')->childrenHierarchy();

        return $this->render('VMBPresentationBundle:Topic:index.html.twig', array(
            'mainTitle' => 'Classification des thématiques',
			'addButtonUrl' => $this->generateUrl('topic_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Topic entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Topic')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Topic entity.');
        }

        return $this->render('VMBPresentationBundle:Topic:show.html.twig', array(
            'entity'      => $entity
        ));
    }

    /**
     * Displays a form to create a new Topic entity.
     *
     */
    public function newAction()
    {
        $topic = new Topic();

        return $this->renderForm($topic);
    }

    /**
     * Displays a form to edit an existing Topic entity.
     *
     */
    public function editAction($id)
    {
        $topic = $this->getTopic($id);

		return $this->renderForm($topic);
    }

    /**
     * Finds and displays a Topic entity.
     */
    protected function renderForm($topic)
    {
		$request = $this->get('request');
		
		$form = $this
			->get('form.factory')
			->create(new TopicType(), $topic);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$flashMessage = ($topic->getId() == null) ? 'Thématique ajoutée' : 'Thématique modifiée';
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($topic);
				$topic->preUpload();
				$em->flush();
				$topic->upload();

				
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('topic'));
			}
		}

		return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($topic->toString())) ? 'Ajout d\'une thématique' : 'Modification d\'une thématique '.$topic->toString()),
				'backButtonUrl' => $this->generateUrl('topic')
			));
    }
    /**
     * Deletes a Topic entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $topic = $this->getTopic($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($topic);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Thématique supprimée');
			} catch (\Exception $e) {
				dump($e);
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('topic'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'la thématique "'.$topic->toString().'" [ATTENTION CELA SUPPRIMERA TOUTES LES MATRICES, RESSOURCES ET PRESENTATIONS LIEES]',
			'mainTitle' => 'Suppression d\'une thématique',
			'backButtonUrl' => $this->generateUrl('topic')
		));
    }

    /**
     * Retrieve an existing Topic entity.
     */
    protected function getTopic($id)
    {
        $topic = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Topic')->find($id);

		if ($topic == null) {
			throw $this->createNotFoundException('Unable to find Topic entity.');
		}

		return $topic;
    }
}
