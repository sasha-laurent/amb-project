<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\PresentationBundle\Entity\Matrix;
use VMB\PresentationBundle\Entity\Pov;
use VMB\PresentationBundle\Entity\Level;
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
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		
		if($this->get('security.context')->isGranted('ROLE_ADMIN')) {
			$entities = $em->getRepository('VMBPresentationBundle:Matrix')->findAll();
			$mainTitle = 'Affichage des matrices';
		}
		else {
			$entities = $em->getRepository('VMBPresentationBundle:Matrix')->findBy(array('owner' => $this->getUser()));
			$mainTitle = 'Affichage de vos matrices de présentation';
		}

        return $this->render('VMBPresentationBundle:Matrix:index.html.twig', array(
            'mainTitle' => $mainTitle,
			'addButtonUrl' => $this->generateUrl('matrix_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Matrix entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Matrix entity.');
        }
        
        if($this->get('security.context')->isGranted('ROLE_ADMIN') || $entity->isOwner($this->getUser())) {					
			$validResources = $em->getRepository('VMBResourceBundle:Resource')->findByTopicSortedByType($entity->getTopic(), true);
			$personalResources = $em->getRepository('VMBResourceBundle:Resource')->findByTopicSortedByType($entity->getTopic(), null, $this->getUser());
			$unofficialResources = $em->getRepository('VMBResourceBundle:Resource')->findByTopicSortedByType($entity->getTopic(), false, $this->getUser(), true);

			return $this->render('VMBPresentationBundle:Matrix:show.html.twig', array(
				'mainTitle' => $entity->getTitle(),
				'backButtonUrl' => $this->generateUrl('matrix'),
				'editButtonUrl' => $this->generateUrl('matrix_edit', array('id' => $entity->getId())),
				'delButtonUrl' => $this->generateUrl('matrix_delete', array('id' => $entity->getId())),
				'entity' => $entity,
				'resources' => array('official' => $validResources, 'personal' => $personalResources, 'unofficial' => $unofficialResources),
				'alertDismissible' => true
			));
		}
		else {
			$this->get('request')->getSession()->getFlashBag()->add('danger', 'Vous ne disposez pas des droits suffisants pour effectuer cette opération');
			return $this->redirect($this->generateUrl('matrix'));
		}
    }
    
    /**
     * Update a matrix used resources
     * Browse values generated by jQuery to know which coordinates are concerned and which resource
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function updateAction(Request $request, $id)
    {
		$em = $this->getDoctrine()->getManager();

		$resourceUpdates = $request->request->all();
		
		$nbRm = $nbAdd = 0;
		
		// We browse the existing used resources to see if they have been modified or removed
		$usedResources = $em->getRepository('VMBPresentationBundle:UsedResource')->findByMatrixId($id);
		foreach($usedResources as $usedRes) {
			$key = $usedRes->getPov()->getId().'_'.$usedRes->getLevel()->getId();
			
			// We make sure that the value is in the right range
			if(isset($resourceUpdates[$key]) && is_numeric($resourceUpdates[$key])) {
				// Conversion to int before tests
				$resourceUpdates[$key] = intval($resourceUpdates[$key]);
				if($resourceUpdates[$key] >= 0) {				
					// We must delete the entity
					if($resourceUpdates[$key] == 0) {
						$em->remove($usedRes);
						$nbRm++;
					}
					// We must update the entity
					else {
						$usedRes->setResource($em->getRepository('VMBResourceBundle:Resource')->find($resourceUpdates[$key]));
					}
					unset($resourceUpdates[$key]);
				}
			}
		}
		
		// We browse the remaining post values > the remaining values that are not 0 are meant to be added to the database
		foreach($resourceUpdates as $key => $res) {
			if(preg_match('`^([0-9]+)_([0-9]+)$`', $key, $matches)) {
				$pov = intval($matches[1]);
				$lvl = intval($matches[2]);
				$res = intval($res);
				
				if($res != 0) {
					$usedResource = new UsedResource($em->getRepository('VMBPresentationBundle:Matrix')->find($id));
					$usedResource->setPov($em->getRepository('VMBPresentationBundle:Pov')->find($pov));
					$usedResource->setLevel($em->getRepository('VMBPresentationBundle:Level')->find($lvl));
					$usedResource->setResource($em->getRepository('VMBResourceBundle:Resource')->find($res));
					
					$em->persist($usedResource);
					$nbAdd++;
				}
			}
		}

		$em->flush();
		
		$flashMessage = 'Matrice modifiée avec succès - '.$nbAdd.' ajouts | '.$nbRm.' retraits';
		$request->getSession()->getFlashBag()->add('success', $flashMessage);
		return $this->redirect($this->generateUrl('matrix_show', array('id' => $id)));
    }

    /**
     * Displays a form to create a new Matrix entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function editAction($id)
    {
        $matrix = $this->getMatrix($id);

		if($this->get('security.context')->isGranted('ROLE_ADMIN') || $matrix->isOwner($this->getUser())) {		
			return $this->renderForm($matrix);
		}
		else {
			$this->get('request')->getSession()->getFlashBag()->add('danger', 'Vous ne disposez pas des droits suffisants pour effectuer cette opération');
			return $this->redirect($this->generateUrl('matrix'));
		}
    }

    /**
     * Finds and displays a Matrix entity.
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    protected function renderForm($matrix)
    {
		$request = $this->get('request');
		$options = array();
		
		if($matrix->getId() != null) {
			$options = array('action'=> $this->generateUrl('matrix_edit', array('id' => $matrix->getId())));
		}
		
		$form = $this
			->get('form.factory')
			->create(new MatrixType(), $matrix, $options);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$matrix->setOwner($this->getUser());
				$em->persist($matrix);
				$em->flush();

				$flashMessage = !$matrix->toString() ? 'Matrice ajoutée' : 'Matrice modifiée';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('matrix_show', array('id' => $matrix->getId())));
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
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
