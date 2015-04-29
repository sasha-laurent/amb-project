<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\PresentationBundle\Entity\Matrix;
use VMB\PresentationBundle\Entity\UsedResource;
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
            'mainTitle' => 'Affichage des matrices',
			'addButtonUrl' => $this->generateUrl('matrix_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Matrix entity.
     *
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Matrix entity.');
        }
        
        $usedResource = new UsedResource($entity);
        $formBuilder = $this->get('form.factory')->createBuilder('form', $usedResource);
        $formBuilder
            ->add('pov', 'entity', array(
				'label' => 'Point de vue',
				'class' => 'VMB\PresentationBundle\Entity\Pov',
				'property' => 'title',
				'choices' => $entity->getPovs()))
			->add('level', 'entity', array(
				'label' => 'Niveau',
				'class' => 'VMB\PresentationBundle\Entity\Level',
				'property' => 'title',
				'choices' => $entity->getLevels()))
			->add('resource', 'entity', array(
				'label' => 'Ressource',
				'class' => 'VMB\ResourceBundle\Entity\Resource',
				'property' => 'title'))
			->add('ajouter', 'submit')
        ;
        
        $form = $formBuilder->getForm();
        
        $form->handleRequest($request);
		if ($form->isValid()) {
			$em->persist($usedResource);
			$em->flush();
			
			$entity->addResource($usedResource);

			$request->getSession()->getFlashBag()->add('success', 'Ressource ajoutée');
		}

        return $this->render('VMBPresentationBundle:Matrix:show.html.twig', array(
            'mainTitle' => 'Matrice "'.$entity->getTitle().'"',
			'backButtonUrl' => $this->generateUrl('matrix'),
			'editButtonUrl' => $this->generateUrl('matrix_edit', array('id' => $entity->getId())),
			'delButtonUrl' => $this->generateUrl('matrix_delete', array('id' => $entity->getId())),
			'entity' => $entity,
			'resourceForm' => $form->createView()
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

				$flashMessage = !$matrix->toString() ? 'Matrice ajoutée' : 'Matrice modifiée';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('matrix'));
			}
		}

		return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($matrix->toString())) ? 'Ajout d\'une matrice' : 'Modification d\'une matrice '.$matrix->toString()),
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
				
				$request->getSession()->getFlashBag()->add('success', 'Matrice supprimée');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('matrix'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'la matrice "'.$matrix->toString().'"',
			'mainTitle' => 'Suppression de la matrice '.$matrix->toString(),
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
