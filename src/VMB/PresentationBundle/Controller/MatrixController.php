<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\PresentationBundle\Entity\Matrix;
use VMB\PresentationBundle\Form\MatrixType;

/**
 * Matrix controller.
 *
 */
class MatrixController extends Controller
{

    /**
     * Lists all Matrix entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VMBPresentationBundle:Matrix')->findAll();

        return $this->render('VMBPresentationBundle:Matrix:index.html.twig', array(
            'mainTitle' => 'Affichage Matrix',
			'addButtonUrl' => $this->generateUrl('matrix_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Matrix entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Matrix')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Matrix entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('VMBPresentationBundle:Matrix:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Matrix entity.
     *
     */
    public function newAction()
    {
        $matrix = new Matrix();

        return $this->renderForm($matrix);
    }

    /**
     * Displays a form to edit an existing Matrix entity.
     *
     */
    public function editAction($id)
    {
        $matrix = $this->getMatrix($id);

		return $this->renderForm($matrix);
    }

    /**
     * Finds and displays a Matrix entity.
     */
    protected function renderForm($matrix)
    {
		$request = $this->get('request');
		
		$form = $this
			->get('form.factory')
			->create(new MatrixType(), $matrix);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$em->persist($matrix);
				$em->flush();

				$flashMessage = !$matrix->toString() ? 'Matrix added' : 'Matrix modified';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('matrix'));
			}
		}

		return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($matrix->toString())) ? 'Ajout d\'un matrix' : 'Modification du matrix '.$matrix->toString()),
				'backButtonUrl' => $this->generateUrl('matrix')
			));
    }
    /**
     * Deletes a Matrix entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $matrix = $this->getMatrix($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($matrix);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Matrix deleted');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('matrix'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'le matrix "'.$matrix->toString().'"',
			'mainTitle' => 'Suppression du matrix '.$matrix->toString(),
			'backButtonUrl' => $this->generateUrl('matrix')
		));
    }

    /**
     * Retrieve an existing Matrix entity.
     */
    protected function getMatrix($id)
    {
        $matrix = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Matrix')->find($id);

		if ($matrix == null) {
			throw $this->createNotFoundException('Unable to find Matrix entity.');
		}

		return $matrix;
    }
}
