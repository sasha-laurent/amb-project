<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\PresentationBundle\Entity\Matrix;
use VMB\PresentationBundle\Entity\Pov;
use VMB\PresentationBundle\Entity\Level;
use VMB\PresentationBundle\Entity\UsedResource;
use VMB\PresentationBundle\Form\MatrixType;

/**
 * Matrix controller.
 *
 * TODO: A user can only edit/delete a matrix which doesn't have any depending presentations -> remove said user from owner, set owner to admin?
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
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		
		$entities = $em->getRepository('VMBPresentationBundle:Matrix')->findAllWithTopics($this->getUser());

        return $this->render('VMBPresentationBundle:Matrix:index.html.twig', array(
            'mainTitle' => $this->get('translator')->trans('matrix.main_title'),
			'backButtonUrl' => $this->get('vmb_presentation.previous_url')->getPreviousUrl($request),
			'addButtonUrl' => $this->generateUrl('matrix_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Matrix entity.
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Matrix')));
        }
        
        if($this->get('security.context')->isGranted('ROLE_ADMIN') || $entity->isOwner($this->getUser())) {					
			$validResources = $em->getRepository('VMBResourceBundle:Resource')->findByTopicSortedByType($entity->getTopic(), true);
			$personalResources = $em->getRepository('VMBResourceBundle:Resource')->findByTopicSortedByType($entity->getTopic(), ($entity->getOfficial() ? true : null), $this->getUser());
			$unofficialResources = $em->getRepository('VMBResourceBundle:Resource')->findByTopicSortedByType($entity->getTopic(), false, $this->getUser(), true);
			$caddyResources = $this->getUser()->getResource();


			return $this->render('VMBPresentationBundle:Matrix:show.html.twig', array(
				'mainTitle' => $entity->getTitle(),
				'backButtonUrl' => $this->container->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('matrix')),
				'editButtonUrl' => $this->generateUrl('matrix_edit', array('id' => $entity->getId())),
				'delButtonUrl' => $this->generateUrl('matrix_delete', array('id' => $entity->getId())),
				'entity' => $entity,
				'resources' => array('official' => $validResources, 'personal' => $personalResources, 'unofficial' => $unofficialResources, 'bookmarks' => $caddyResources),
				'forkButtonUrl' => $this->generateUrl('matrix_copy', array('id' => $id)),
				'hasPlaybackFunction' => true
			));
		}
		else {
			$this->get('request')->getSession()->getFlashBag()->add('danger', $this->get('translator')->trans('message.error.not_enough_rights'));
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
		$matrix = $em->getRepository('VMBPresentationBundle:Matrix')->find($id);

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
					$resource = $em->getRepository('VMBResourceBundle:Resource')->find($res);
						
					// We allow to add a resource to an official matrix only if it's trusted
					if(!$matrix->getOfficial() || $resource->getTrusted()) {
						$usedResource = new UsedResource($matrix);
						$usedResource->setPov($em->getRepository('VMBPresentationBundle:Pov')->find($pov));
						$usedResource->setLevel($em->getRepository('VMBPresentationBundle:Level')->find($lvl));
						$usedResource->setResource($resource);
						
						$em->persist($usedResource);
						$nbAdd++;
					}
					else {
						$request->getSession()->getFlashBag()->add('danger', $this->get('translator')->trans('matrix.error.resource_unofficial_not_added'));
					}
				}
			}
		}

		$em->flush();
		
		$flashMessage = $this->get('translator')->trans('matrix.modified').' - '.$nbAdd.' '. $this->get('translator')->trans('added') . ' | '.$nbRm.' '. $this->get('translator')->trans('removed');
		$request->getSession()->getFlashBag()->add('success', $flashMessage);
		return $this->redirect($this->generateUrl('matrix_show', array('id' => $id)));
    }

    /**
     * Displays a form to create a new Matrix entity.
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    public function newAction()
    {
        $matrix = new Matrix();

        return $this->renderForm($matrix);
    }

    /**
     * Displays a form to edit an existing Matrix entity.
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @param is_modal: means no Back button, but Close button instead.
     */
    public function editAction($id, $is_modal = false)
    {
        $matrix = $this->getMatrix($id);

		if($this->get('security.context')->isGranted('ROLE_ADMIN') 
			|| $matrix->isOwner($this->getUser())) 
		{		
			return $this->renderForm($matrix, $is_modal);
		} else {
			$this->get('request')->getSession()->getFlashBag()->add('danger', 
				$this->get('translator')->trans('message.error.not_enough_rights'));
			return $this->redirect($this->generateUrl('matrix'));
		}
    }

    /*
    ** @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    ** Copies a Matrix for the logged in user to possibly apply his 
    ** or her own modifications and redirects him/her to the copy.
    ** TODO: Set successive copies as a series in parentheses (could lookup matrix copies with same user name suffix?)
    */
    public function copyAction(Request $request, $id){
		$translator = $this->get('translator');
    	try {
				$em = $this->getDoctrine()->getManager();
				$matrix = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($id);

        		if (!$matrix) {
            	throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Matrix')));
        		}
				$em->detach($matrix);
    					// Copy the matrix
				$newMatrix = clone $matrix;
				
				// PoVs
				$newPovs = array();
				foreach($newMatrix->getPovs() as $pov) {
					$newPovs[$pov->getId()] = clone $pov;
					$newPovs[$pov->getId()]->setMatrix($newMatrix);
				}
				
				$newMatrix->clearPovs();
				foreach($newPovs as $newPov) {
					$newMatrix->addPov($newPov);
				}
				
				// Levels
				$newLvls = array();
				foreach($newMatrix->getLevels() as $lvl) {
					$newLvls[$lvl->getId()] = clone $lvl;
					$newLvls[$lvl->getId()]->setMatrix($newMatrix);
				}
				
				$newMatrix->clearLevels();
				foreach($newLvls as $newLvl) {
					$newMatrix->addLevel($newLvl);
				}
				
				// Used Resources
				$newResources = array();
				foreach($newMatrix->getResources() as $res) {
					$newResources[$res->getId()] = clone $res;
					$newResources[$res->getId()]->setPov($newPovs[$res->getPov()->getId()]);
					$newResources[$res->getId()]->setLevel($newLvls[$res->getLevel()->getId()]);
					$newResources[$res->getId()]->setMatrix($newMatrix);
				}
				
				$newMatrix->clearResources();
				foreach($newResources as $newRes) {
					// no cascade persist, we must do it here
					$newMatrix->addResource($newRes);
					$em->persist($newRes);
				}
				
				$newMatrix->setOwner($this->getUser());
				$newMatrix->setDateCreation(new \DateTime());
				$newMatrix->setDateUpdate(new \DateTime());
				$newTitle = $newMatrix->getTitle() . " - " . $this->getUser()->toString(). "'s copy"; 
				$newMatrix->setTitle($newTitle);
				$em->persist($newMatrix);
				$em->flush();

				$request->getSession()->getFlashBag()->add('success', $translator->trans('matrix.copy_success'));				

			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()
				->add('danger', $e); // $translator->trans('message.error.occured')
				return $this->showAction($request, $id);
			}
		return $this->showAction($request, $newMatrix->getId());
    }

    /**
     * Finds and displays a Matrix entity.
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     */
    protected function renderForm($matrix, $is_modal_dialog = false)
    {
		$request = $this->get('request');
		$options = array();
		$translator = $this->get('translator');
		
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

				$flashMessage = !$matrix->toString() ? 
					$translator->trans('matrix.added') : $translator->trans('matrix.modified');
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('matrix_show', array('id' => $matrix->getId())));
			}
		}
		$render_opts = array(
			'form' => $form->createView(),
			'entity' => $matrix,
			'mainTitle' => ((!($matrix->toString())) ? 
				$translator->trans('matrix.add') : $translator->trans('matrix.edit')));

		if($is_modal_dialog){
			$render_opts['saveButton'] = true;		
			$render_opts['is_modal'] = true;		
			$render_opts['delButtonUrl'] = '#" data-dismiss="modal"'; // small hack to inject the dismiss attribute
			return $this->render('VMBPresentationBundle:Matrix:modalEdit.html.twig', $render_opts);	
		} else {
			$render_opts['backButtonUrl'] = $this->container->get('vmb_presentation.previous_url')
			->getPreviousUrl($request, $this->generateUrl('matrix'));
			return $this->render('::Backend/form.html.twig', $render_opts);
		}
    }
    
    /**
     * Set a matrix as official
     *
     */
    /**
    * @Security("is_granted('ROLE_TEACHER')")
    */
    public function officialAction(Request $request, $id)
    {
		$translator = $this->get('translator');
		
		$em = $this->getDoctrine()->getManager();
        $matrix = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($id);
        if($matrix  == null) {
			$request->getSession()->getFlashBag()->add('danger', $translator->trans('message.error.occured'));
			return $this->redirect($this->generateUrl('matrix'));
		}

		if ($request->isMethod('POST')) {
			try {
				
				$valid = true;
				$resourcesDenied = array();
				foreach($matrix->getResources() as $usedResource) {
					if(!$usedResource->getResource()->getTrusted()) {
						$valid = false;
						$resourcesDenied[] = $usedResource->getResource();
					}
				}
				
				if($valid) {
					$matrix->setOfficial(true);
					$em->flush();
					
					$request->getSession()->getFlashBag()->add('success', $translator->trans('matrix.official_success'));
				}
				else {
					$request->getSession()->getFlashBag()->add('danger', $translator->trans('matrix.official_fail'));
				}
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger', $translator->trans('message.error.occured'));
			}
			return $this->redirect($this->generateUrl('matrix'));
		}

		// Si la requête est en GET, on affiche une page de confirmation
		return $this->render('VMBPresentationBundle:Matrix:official.html.twig', array(
			'mainTitle' => $translator->trans('matrix.official_title'),
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
		$translator = $this->get('translator');
        $matrix = $this->getMatrix($id);
        
        if($this->get('security.context')->isGranted('ROLE_ADMIN') || $matrix->isOwner($this->getUser())) {	

			if ($request->isMethod('POST')) {
				try {
					$em = $this->getDoctrine()->getManager();
					$em->remove($matrix);
					$em->flush();
					
					$request->getSession()->getFlashBag()->add('success', $translator->trans('matrix.deleted'));
				} catch (\Exception $e) {
					$request->getSession()->getFlashBag()->add('danger', $translator->trans('message.error.occured'));
				}
				return $this->redirect($this->generateUrl('matrix'));
			}

			// Si la requête est en GET, on affiche une page de confirmation avant de delete
			// TODO: Fix previous url call (doesn't redirect back to matrix/show/{id} )
			return $this->render('::Backend/delete.html.twig', array(
				'entityTitle' => '"'.$matrix->toString().'"',
				'mainTitle' => $translator->trans('matrix.delete'),
				'backButtonUrl' => $this->container->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('matrix'))
			));
		}
		else {
			return $this->redirect($this->generateUrl('matrix'));
		}
    }
    
    /**
     * Deletes a Matrix Row (level or Pov)
     * 
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function deleteRowAction(Request $request)
    {
		$matrixId = $request->request->get('matrixId');
		$matrix = $this->getMatrix($matrixId);
		
		if($this->get('security.context')->isGranted('ROLE_ADMIN') 
			|| $matrix->isOwner($this->getUser())) {	
			if ($request->isMethod('POST')) {
				$rowId = $request->request->get('rowId');
				$type = $request->request->get('rowType');
				// Check data integrity
				if($type != ('Level' || 'Pov'))
				{
					return new JsonResponse("Invalid row type",500);
				} else if ($rowId < 0) {
					return new JsonResponse("Invalid row id", 500);
				}
				try {
					$em = $this->getDoctrine()->getManager();
					// Bug: rowId from form is not real rowId from database table
					// If the element was found
					if($type == 'Pov'){
					/* VMB\PresentationBundle\Entity\Pov */
						$matrix_row = $matrix->removePovAtIndex($rowId);
					} else if($type == 'Level'){
					/* VMB\PresentationBundle\Entity\Level */
						$matrix_row = $matrix->removeLevelAtIndex($rowId);
					}
					if(null === $matrix_row){ // Or boolean?
						// Do something
					}
					// Getting the real table row id
					$db_row_id = $matrix_row->getId();
					/* VMB\PresentationBundle\Entity\MatrixRow */
					$db_elt = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:'.$type)->find($db_row_id);

					if($db_elt !== null){
						// Apply database modification
						$em->remove($db_elt);
						$em->persist($matrix);
						$em->flush();
						return new JsonResponse('['.$db_elt->getId().':'.$db_elt->getTitle().'] removed successfully');
					} else {
						return new JsonResponse('Element not found', 500);
					}
				} catch (\Exception $e) {
					return new JsonResponse($e->getMessage(), 500); 
				}
			}
		} else {
			$access_denied = $this->get('translator')->trans('message.error.not_enough_rights');
			return new JsonResponse($access_denied, 403);
		}
	}
	
	/**
     * Edit a Matrix Row (level or Pov)
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function editRowAction(Request $request)
    {
		$matrixId = $request->request->get('matrixId');
		$matrix = $this->getMatrix($matrixId);
		
		if($this->get('security.context')->isGranted('ROLE_ADMIN') || $matrix->isOwner($this->getUser())) {	
			if ($request->isMethod('POST')) {
				$rowId = intval($request->request->get('rowId'));
				$type = $request->request->get('rowType');
				$title = $request->request->get('title');
				
				try {
					$em = $this->getDoctrine()->getManager();
					if($rowId == 0) {
						$elt = null;
						if($type == 'Level' || $type == 'Pov') {
							if($type == 'Pov') {
								$elt = new Pov();
							}
							else {
								$elt = new Level();
							}
							$elt->setMatrix($matrix);
							$em->persist($elt);
						}
					} else {
						$elt = $em->getRepository('VMBPresentationBundle:'
							.$type)->find($rowId);
					}
					
					// If the element was found
					if($elt !== null && 
						$elt->getMatrix()->getId() == intval($matrixId)) {
						$elt->setTitle($title);
						$em->flush();
						return new Response($elt->getId());	
					}
				} catch (\Exception $e) {
					return new Response($e); 
				}
			}
		}
		return new Response('error');
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
			throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Matrix')));
		}

		return $matrix;
    }
}
