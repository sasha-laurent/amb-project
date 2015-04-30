<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        
        $resources = $em->getRepository('VMBResourceBundle:Resource')->findAll();

        return $this->render('VMBPresentationBundle:Matrix:show.html.twig', array(
            'mainTitle' => 'Matrice "'.$entity->getTitle().'"',
			'backButtonUrl' => $this->generateUrl('matrix'),
			'editButtonUrl' => $this->generateUrl('matrix_edit', array('id' => $entity->getId())),
			'delButtonUrl' => $this->generateUrl('matrix_delete', array('id' => $entity->getId())),
			'entity' => $entity,
			'resources' => $resources
		));
    }
    
    /**
     * Update a matrix resources [AJAX only]
     *
     */
    public function updateAction(Request $request, $id)
    {
		if($request->isXmlHttpRequest() || true)
		{
			$em = $this->getDoctrine()->getManager();
			
			$toBeAdded = array();
			$toBeDeleted = array();
			$resourceUpdates = $request->request->all();
			
			$nbRm = $nbAdd = 0;
			
			// We go through all data received and we part them into two lists
			foreach($resourceUpdates as $info => $action) {
				if(preg_match('`^([0-9]+)_([0-9]+)_([0-9]+)$`', $info, $matches)) {
					$pov = $matches[1];
					$lvl = $matches[2];
					$res = $matches[3];
					
					if($action == 'rm') {
						if(isset($toBeDeleted[$pov]) && !is_array($toBeDeleted[$pov])) {
							$toBeDeleted[$pov] = array();
						}
						$toBeDeleted[$pov][$lvl] = $res;
					}
					elseif($action == 'add') {
						if(isset($toBeAdded[$pov]) && !is_array($toBeAdded[$pov])) {
							$toBeAdded[$pov] = array();
						}
						$toBeAdded[$pov][$lvl] = $res;
					}
				}
			}
			
			// We delete the usedResource meant to be removed
			foreach($toBeDeleted as $pov => $subArr) {
				foreach($subArr as $lvl => $res) {
					$usedResource = $em->getRepository('VMBPresentationBundle:UsedResource')->findByCoordinates($id, $pov, $lvl, $res);
					$em->remove($usedResource);
					$nbRm++;
				}
			}
			
			// We add the new resources
			foreach($toBeAdded as $pov => $subArr) {
				foreach($subArr as $lvl => $res) {
					$usedResource = new UsedResource($em->getRepository('VMBPresentationBundle:Matrix')->find($id));
					$usedResource->setPov($em->getRepository('VMBPresentationBundle:Pov')->find($pov));
					$usedResource->setLevel($em->getRepository('VMBPresentationBundle:Level')->find($lvl));
					$usedResource->setResource($em->getRepository('VMBResourceBundle:Resource')->find($res));
					
					$em->persist($usedResource);
					$nbAdd++;
				}
			}
			$em->flush();
			
			return new Response('Remove : '.$nbRm.' - Add : '. $nbAdd.'<br/>'.print_r($toBeAdded, true).'<hr/>'.print_r($toBeDeleted, true));
		}
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
				'mainTitle' => ((!($matrix->toString())) ? 'Ajout d\'une matrice' : 'Modification d\'une matrice'),
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
			'mainTitle' => 'Suppression d\'une matrice',
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
