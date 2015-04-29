<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\PresentationBundle\Entity\Presentation;
use VMB\PresentationBundle\Form\PresentationType;

/**
 * Presentation controller.
 *
 */
class PresentationController extends Controller
{

    /**
     * Lists all Presentation entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->findAll();

        return $this->render('VMBPresentationBundle:Presentation:index.html.twig', array(
            'mainTitle' => 'Affichage Presentation',
			'addButtonUrl' => $this->generateUrl('presentation_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Presentation entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('VMBPresentationBundle:Presentation:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Presentation entity.
     *
     */
    public function newAction()
    {
        $presentation = new Presentation();

        return $this->renderForm($presentation);
    }

    /**
     * Displays a form to edit an existing Presentation entity.
     *
     */
    public function editAction($id)
    {
        $presentation = $this->getPresentation($id);

		return $this->renderForm($presentation);
    }

    /**
     * Finds and displays a Presentation entity.
     */
    protected function renderForm($presentation)
    {
		$request = $this->get('request');
		
		$form = $this
			->get('form.factory')
			->create(new PresentationType(), $presentation);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$em->persist($presentation);
				$em->flush();

				$flashMessage = !$presentation->toString() ? 'Presentation added' : 'Presentation modified';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('presentation'));
			}
		}

		return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($presentation->toString())) ? 'Ajout d\'un presentation' : 'Modification du presentation '.$presentation->toString()),
				'backButtonUrl' => $this->generateUrl('presentation')
			));
    }
    /**
     * Deletes a Presentation entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $presentation = $this->getPresentation($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($presentation);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Presentation deleted');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('presentation'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'le presentation "'.$presentation->toString().'"',
			'mainTitle' => 'Suppression du presentation '.$presentation->toString(),
			'backButtonUrl' => $this->generateUrl('presentation')
		));
    }

    /**
     * Retrieve an existing Presentation entity.
     */
    protected function getPresentation($id)
    {
        $presentation = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Presentation')->find($id);

		if ($presentation == null) {
			throw $this->createNotFoundException('Unable to find Presentation entity.');
		}

		return $presentation;
    }
}
