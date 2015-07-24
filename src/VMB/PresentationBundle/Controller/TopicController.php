<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Controller\Exception;
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
        # TODO: Add counts
        return $this->render('VMBPresentationBundle:Topic:index.html.twig', array(
            'mainTitle' => $this->get('translator')->trans('topic.main_title'),
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
            throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Topic')));
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
        $em = $this->getDoctrine()->getManager();

        $topic = $this->getTopic($id);

		return $this->renderForm($topic);
    }

    /**
     * Finds and displays a Topic entity.
     */
    protected function renderForm($topic)
    {
		$request = $this->get('request');
		$translator = $this->get('translator');
		
		$form = $this
			->get('form.factory')
			->create(new TopicType(), $topic);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
                if($topic->getParent() != $topic){
    				$flashMessage = ($topic->getId() == null) ? 
                        $translator->trans('topic.added') : $translator->trans('topic.modified');
    				$em = $this->getDoctrine()->getManager();
    				$em->persist($topic);
    				$topic->preUpload();
    				$em->flush();
    				$topic->upload();

				
				    $request->getSession()->getFlashBag()->add('success', $flashMessage);
				    return $this->redirect($this->generateUrl('topic'));
                } else {
                    $request->getSession()->getFlashBag()->add('danger', $translator->trans('message.error.occured'));
                }
			}
		}

		return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($topic->toString())) ? $translator->trans('topic.add') : $translator->trans('topic.edit')),
				'backButtonUrl' => $this->generateUrl('topic')
			));
    }
    /**
     * Deletes a Topic entity.
     * @Security("is_granted('ROLE_ADMIN')")
     * 
     */
    public function deleteAction(Request $request, $id)
    {
        $topic = $this->getTopic($id);
        $translator = $this->get('translator');

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
                    $em->remove($topic);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('success', 
                        $translator->trans('topic.deleted'));   
			} catch (\Exception $e) {
				dump($e);
				$request->getSession()->getFlashBag()->add('danger', 
                    $translator->trans('message.error.occured'));
			}
			return $this->redirect($this->generateUrl('topic'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => $translator->trans('topic.deleteMessage', array('%title%' => $topic->toString())),
			'mainTitle' => $translator->trans('topic.delete'),
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
			throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Topic')));
		}

		return $topic;
    }
}
