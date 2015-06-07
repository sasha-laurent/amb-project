<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\PresentationBundle\Entity\Presentation;
use VMB\PresentationBundle\Entity\Matrix;
use VMB\PresentationBundle\Entity\Level;
use VMB\PresentationBundle\Entity\Pov;
use VMB\PresentationBundle\Entity\UsedResource;
use VMB\PresentationBundle\Entity\CheckedResource;
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
    /**
	* @Security("has_role('ROLE_ADMIN')")
	*/
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
		
		
		$request = $this->get('request');
		if($request->query->get('default') != null && $request->query->get('id') != null) { 
			$this->setPresentationDefaultValue($request->query->get('id'), $request->query->get('default'));
		}
		if($request->query->get('public') != null && $request->query->get('id') != null) { 
			$this->setPresentationPublicValue($request->query->get('id'), $request->query->get('public'));
		}
		if($request->query->get('official') != null && $request->query->get('id') != null) { 
			$this->setPresentationOfficialValue($request->query->get('id'), $request->query->get('official'));
		}
        
        if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// Ici je fixe le nombre d'annonces par page à 3
		// Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
		$nbPerPage = 12;

		// On récupère notre objet Paginator
        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->getPresentations($page, $nbPerPage, null, 'all', 'all', 'all');

		// On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);

		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages  && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBPresentationBundle:Presentation:index.html.twig', array(
            'mainTitle'=> 'Toutes les présentations',
            'entities' => $entities,
			'nbPages'  => $nbPages,
			'page'     => $page
        ));
    }
    
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function browseAction($page, $topic=null)
    {
		if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		$nbPerPage = 12;
		$em = $this->getDoctrine()->getManager();
		$topics = $em->getRepository('VMBPresentationBundle:Topic')->childrenHierarchy();
		
		
		$mainTitle = 'Parcourir les présentations';
		if($topic != null) {
			$topic = $em->getRepository('VMBPresentationBundle:Topic')->find($topic);
			$mainTitle = $topic->getTitle().' - Présentations';
		}
		
		$request = $this->get('request');
		$official = ($request->query->get('official') == 1) ? true : 'all';
		$default = ($request->query->get('default') == 1) ? true : 'all';
		$search = $request->query->get('search');
		
		$publicMode = true;
		$personal = null;
		$privateEntities = null;
		if($request->query->get('personal') == 1) {
			$personal = $this->getUser();
			$publicMode = 'all';
		}
		
        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->getPresentations($page, $nbPerPage, $topic, $publicMode, $official, $default, $personal, $search);
        
        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);
		
		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBPresentationBundle:Presentation:browseTopic.html.twig', array(
            'mainTitle' => $mainTitle,
            'topic' 	=> $topic,
            'topics' 	=> $topics,
            'entities' 	=> $entities,
            'search' 	=> $search,
			'nbPages'  	=> $nbPages,
			'page'     	=> $page
        ));
    }
    
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function personalIndexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
		
		$request = $this->get('request');
		if($request->query->get('default') != null && $request->query->get('id') != null) { 
			$this->setPresentationDefaultValue($request->query->get('id'), $request->query->get('default'));
		}
		if($request->query->get('public') != null && $request->query->get('id') != null) { 
			$this->setPresentationPublicValue($request->query->get('id'), $request->query->get('public'));
		}
        
        if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// Ici je fixe le nombre d'annonces par page à 3
		// Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
		$nbPerPage = 12;

		// On récupère notre objet Paginator
        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->getPresentations($page, $nbPerPage, null, 'all', 'all', 'all', $this->getUser());

		// On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);

		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBPresentationBundle:Presentation:personalIndex.html.twig', array(
            'mainTitle'=> 'Mes présentations',
            'entities' => $entities,
			'nbPages'  => $nbPages,
			'page'     => $page
        ));
    }
    
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function deepCopyAction(Request $request, $id)
    {
		$presentation = null;
		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$presentation = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);
				$matrix = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($presentation->getMatrix()->getId());
				$em->detach($presentation);
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
				
				
				// We now manage the presentation and the checked resources
				$newPresentation = clone $presentation;
				$newPresentation->setMatrix($newMatrix);
				$newPresentation->setDateCreation(new \DateTime());
				$newPresentation->setDateUpdate(new \DateTime());
				$newPresentation->setOwner($this->getUser());
				$newPresentation->setPublic(false);
				$newPresentation->setOfficial(false);
				$newPresentation->setSlug(null);
				
				// Checked Resources
				$newCheckedResources = array();
				foreach($newPresentation->getResources() as $res) {
					$newCheckedResources[$res->getId()] = clone $res;
					$newCheckedResources[$res->getId()]->setPresentation($newPresentation);
					$newCheckedResources[$res->getId()]->setUsedResource($newResources[$res->getUsedResource()->getId()]);
				}
				
				$newPresentation->clearResources();
				foreach($newCheckedResources as $newRes) {
					// cascade persist here
					$newPresentation->addResource($newRes);
				}
				
				$em->persist($newMatrix);
				$em->persist($newPresentation);
				
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Copie de la présentation réussie');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('presentation_perso'));
		}
		else {
			$presentation = $this->getPresentation($id);
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('VMBPresentationBundle:Presentation:copy.html.twig', array(
			'entityTitle' => '"'.$presentation->toString().'"',
			'mainTitle' => 'Copie de la présentation '.$presentation->toString(),
			'backButtonUrl' => $this->generateUrl('presentation_perso')
		));
    }
    
    public function displayMatrixesAction()
    {
		$em = $this->getDoctrine()->getManager();
        $matrixes = $em->getRepository('VMBPresentationBundle:Matrix')->findAll();

        return $this->render('VMBPresentationBundle:Presentation:displayMatrixes.html.twig', array(
			'matrixes' => $matrixes
        ));
	}

    /**
     * Finds and displays a Presentation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }
        
        $alternativeResources = array();
        $request = $this->get('request');
		if ($request->isMethod('POST')) 
		{
			$postValues = $request->request->all();
			$matrix = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($entity->getMatrix()->getId());

			foreach($postValues as $key => $position) {
				if(preg_match('`usedResource_([0-9]+)_([0-9]+)`', $key, $matches)) {
					$resId = intval($matches[1]);
					$duration = intval($matches[2]);
					
					$newRes = new CheckedResource();
					$newRes->setUsedResource($matrix->getUsedResourceById($resId));
					$newRes->setDuration($duration);
					
					if($newRes != null) {
						$alternativeResources[] = $newRes;
					}
				}
			}
		}

		$user = $this->getUser();
	
		$args = array(
            'mainTitle' => $entity->getTitle(),
            'saveToCaddy' => 'presentation',
            'inCaddy' => $user->presentationIsInCaddy($entity),
			'entity' => $entity,
			'alternativeResources' => $alternativeResources
        );
        if($entity->isOwner($user)) {
			$args['editButtonUrl'] = $this->generateUrl('presentation_edit', array('id' => $id));
		}
		else {
			$args['forkButtonUrl'] = $this->generateUrl('presentation_deep_copy', array('id' => $id));
		}
        return $this->render('VMBPresentationBundle:Presentation:show.html.twig', $args);
    }
    
    /**
     * Finds and displays a Presentation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function additionalContentAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);
        $matrix = $em->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithSortedResources($entity->getMatrix()->getId());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }
        
        $checkedResourcesId = array();
        $checkedResources = $entity->getResources();
        foreach($checkedResources as $checkedRes) {
			$checkedResourcesId[] = $checkedRes->getUsedResource()->getId();
		}
        
        // We ignore resources already in use in the presentation and prioritize povs without any resource checked
		$sortedResources = $matrix->getSortedResources();
		foreach($sortedResources as $pov => $subArr) {
			$nbResTotal = 0;
			$nbResUsed = 0;
			foreach($subArr as $lvl => $usedRes) {
				$nbResTotal++;
				if(in_array($usedRes->getId(), $checkedResourcesId)) {
					$nbResUsed++;
					unset($sortedResources[$pov][$lvl]);
				}
			}
			
			// Prioritize a pov by putting it at the beginning if it didn't have any resource checked
			if($nbResTotal > 0 && $nbResUsed == 0) {
				$sortedResources = array($pov => $sortedResources[$pov]) + $sortedResources;
			}
			// We delete rows where all resources are already checked
			elseif($nbResTotal == $nbResUsed) {
				unset($sortedResources[$pov]);
			}
		}
		
		$additionalResourcesId = array();
		$i = 0;
		while(count($sortedResources) > 0) {
			$additionalResourcesId[$i] = array();
			foreach($sortedResources as $pov => $subArr) {
				foreach($subArr as $lvl => $usedRes) {
					// We add the first element we find and break
					$additionalResourcesId[$i][] = $sortedResources[$pov][$lvl]->getId();
					unset($sortedResources[$pov][$lvl]);
					break;
				}
			}
			
			// We delete empty rows
			foreach($sortedResources as $pov => $subArr) {
				if(count($subArr) == 0) {
					unset($sortedResources[$pov]);
				}
			}
			$i++;
		}
		
        return $this->render('VMBPresentationBundle:Presentation:complement.html.twig', array(
            'mainTitle' => 'Voir plus - '. $entity->getTitle(),
            'backButtonUrl' => $this->generateUrl('presentation_show', array('id' => $id)),
			'entity' => $entity,
			'matrix' => $matrix,
			'additionalResourcesId' => $additionalResourcesId
        ));
    }

    /**
     * Displays a form to create a new Presentation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function newAction($idMatrix)
    {
		$matrix = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Matrix')->getMatrixWithResources($idMatrix);

		if ($matrix == null) {
			throw $this->createNotFoundException('Unable to find Matrix entity.');
		}
		
        $presentation = new Presentation($matrix);

        return $this->renderForm($presentation);
    }

    /**
     * Displays a form to edit an existing Presentation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function editAction($id)
    {
        $presentation = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Presentation')->findWithAllMatrixResources($id);
		
		if($this->get('security.context')->isGranted('ROLE_ADMIN') || $presentation->isOwner($this->getUser())) {		
			return $this->renderForm($presentation);
		}
		else {
			$this->get('request')->getSession()->getFlashBag()->add('danger', 'Vous ne disposez pas des droits suffisants pour effectuer cette opération');
			return $this->redirect($this->generateUrl('presentation_perso'));
		}
    }
    
    /**
     * Displays a form to create a new presentation from an existing Presentation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function createFromAction($id)
    {
        $presentation = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Presentation')->findWithSortedResources($id);

		return $this->renderForm($presentation, true, $this->generateUrl('presentation_complementary', array('id' => $id)));
    }

    /**
     * Finds and displays a Presentation entity.
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    protected function renderForm($presentation, $saveAsCopy = false, $backUrl = null)
    {
		$request = $this->get('request');
		
		if($saveAsCopy) {
			$presentation->setTitle($presentation->getTitle().' - Présentation personnelle');
		}
		
		$form = $this
			->get('form.factory')
			->create(new PresentationType(), $presentation);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$postValues = $request->request->all();
				
				// Delete thumbs if asked
				if(!$saveAsCopy && intval($postValues['keepThumbs']) == 0) {
					$presentation->deleteThumbs();
				}
				
				
				$workingPresentation = null;
				// If it's a copy, we don't have to worry about modifiying existing objects
				if(!$saveAsCopy) {
					$checkedRes = $presentation->getResources();
					$indexedCheckedRes = array();
					if($checkedRes != null) {
						foreach($checkedRes as $r) {
							$indexedCheckedRes[$r->getUsedResource()->getId()] = $r;
						}
					}
					$workingPresentation = $presentation;
				}
				else {
					$em->detach($presentation);
					$copiedPresentation = new Presentation($presentation->getMatrix());
					$copiedPresentation->setTitle($presentation->getTitle());
					$copiedPresentation->setDescription($presentation->getDescription());				
					$copiedPresentation->setDuration($presentation->getDuration());
					$workingPresentation = $copiedPresentation;
				}
				
				
				$totalDuration = 0;
				foreach($postValues as $key => $position) {
					if(preg_match('`(usedResource|suggestedResource)_([0-9]+)_([0-9]+)`', $key, $matches)) {
						$resId = intval($matches[2]);
						$isSuggested = ($matches[1] == 'suggestedResource');
						$duration = $matches[3];
						
						// If the resource has already been persisted as a checked resource
						if(!$saveAsCopy && isset($indexedCheckedRes[$resId])) {
							$indexedCheckedRes[$resId]->setSort($position);
							$indexedCheckedRes[$resId]->setSuggested($isSuggested);
							$indexedCheckedRes[$resId]->setDuration($duration);
							if(!$isSuggested) {
								$totalDuration += $indexedCheckedRes[$resId]->getDuration();
							}
							
							unset($indexedCheckedRes[$resId]);
						}
						// We ignore suggestions if we're dealing with a new copy (since we can't put suggestions at this stage)
						elseif(($saveAsCopy && !$isSuggested) || !$saveAsCopy) {
							$newCheckedRes = new CheckedResource();
							$newCheckedRes->setPresentation($workingPresentation);
							$workingPresentation->addResource($newCheckedRes);
							$em->persist($newCheckedRes);
							$newCheckedRes->setUsedResource($em->getRepository('VMBPresentationBundle:UsedResource')->find($resId));
							$newCheckedRes->setDuration($duration);
							$newCheckedRes->setSort($position);
							$newCheckedRes->setSuggested($isSuggested);
							if(!$isSuggested) {
								$totalDuration += $newCheckedRes->getDuration();
							}
						}
					}
				}
				
				if(!$saveAsCopy) {
					// If there are still values in this array it means it has to be removed
					foreach($indexedCheckedRes as $r) {
						$workingPresentation->removeResource($r);
						$em->remove($r);
					}
				}
				
				$workingPresentation->setDuration($totalDuration);
				if(!is_numeric($workingPresentation->getId())) {
					$workingPresentation->setOwner($this->getUser());
				}
				$em->persist($workingPresentation);
				$workingPresentation->preUpload();
				$em->flush();
				$workingPresentation->upload();

				$flashMessage = !$workingPresentation->toString() ? 'Présentation ajoutée' : 'Présentation modifiée';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				
				if($saveAsCopy) {
					return $this->redirect($this->generateUrl('presentation_perso'));
				}
				
			}
		}
		
		$shownPresentation = (isset($workingPresentation)) ? $workingPresentation : $presentation;
		
		$parameters = array(
				'form' => $form->createView(),
				'mainTitle' => ((!($presentation->toString())) ? 'Ajout d\'une présentation' : $presentation->toString()),
				'backButtonUrl' => $this->container->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('vmb_presentation_browse')),
				'copy' => $saveAsCopy,
				'matrix' => $presentation->getMatrix(),
				'presentation' => $shownPresentation,
				'alertDismissible' => true
			);
			
		if(!$saveAsCopy && $presentation->getId() != null) {
			$parameters['delButtonUrl'] = $this->generateUrl('presentation_delete', array('id'=> $presentation->getId()));
		}
		
		return $this->render('VMBPresentationBundle:Presentation:edit.html.twig', 
			$parameters);
    }
    /**
     * Deletes a Presentation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function deleteAction(Request $request, $id)
    {
        $presentation = $this->getPresentation($id);
        
        $lastUrl = preg_replace('`(.+)'. $this->generateUrl('vmb_presentation_homepage') .'`', '/', $request->headers->get('referer'));
		$match = $this->get('router')->match($lastUrl);
		$arr = array('route' => $match['_route'], 'args' => array());
		foreach($match as $key => $value) {
			if(substr($key, 0, 1) != '_') {
				$arr['args'][$key] = $value;
			}
		}

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($presentation);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Presentation deleted');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('presentation_perso'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => '"'.$presentation->toString().'"',
			'mainTitle' => 'Suppression de la présentation '.$presentation->toString(),
			'backButtonUrl' => $this->generateUrl('presentation')
		));
    }
    
    public function parameterAction(Request $request, $param, $id, $value) 
	{
		if($param == 'official') {
			$this->setPresentationOfficialValue($id, $value);
		}
		elseif($param == 'default') {
			$this->setPresentationDefaultValue($id, $value);
		}
		elseif($param == 'public') {
			$this->setPresentationPublicValue($id, $value);
		}
		return $this->redirect($this->container->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('vmb_presentation_browse')));
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
    
    /**
     * Test rights of the user and modify if he's allowed
     */
    protected function setPresentationDefaultValue($id, $defaultValue, $flashBags=true)
    {
        $presentation = $this->getPresentation($id);
        
		if($defaultValue != null) {
			// only officials presentations can be set to default
			if($presentation->getOfficial() == 1) {
				// the user must own the presentation or at least be a teacher
				if($this->get('security.context')->isGranted('ROLE_TEACHER') ||$presentation->isOwner($this->getUser())) {
					// check if the value is correct
					$newDefaultValue = intval($defaultValue);
					if(in_array($newDefaultValue, array(0, 1))) {
						// modification
						$presentation->setDefault($newDefaultValue);
						$this->getDoctrine()->getManager()->flush();
					}
				}
				elseif($flashBags) {
					$this->get('request')->getSession()->getFlashBag()->add('danger', 'Vous ne disposez pas de droits suffisants pour effectuer cette opération');
				}
			}
			elseif($flashBags) {
				$this->get('request')->getSession()->getFlashBag()->add('danger', 'Une présentation non officielle ne peut être passée en présentation par défaut');
			}
		}
    }
    
    
    /**
     * Test rights of the user and modify if he's allowed
     */
    protected function setPresentationPublicValue($id, $value, $flashBags=true)
    {
        $presentation = $this->getPresentation($id);
        
		if($value != null) {
			// the user must own the presentation or at least be a teacher
			if($this->get('security.context')->isGranted('ROLE_ADMIN') ||$presentation->isOwner($this->getUser())) {
				// check if the value is correct
				$newValue = intval($value);
				if(in_array($newValue, array(0, 1))) {
					// modification
					$presentation->setPublic($newValue);
					$this->getDoctrine()->getManager()->flush();
				}
			}
			elseif($flashBags) {
				$this->get('request')->getSession()->getFlashBag()->add('danger', 'Vous ne disposez pas de droits suffisants pour effectuer cette opération');
			}
		}
    }
    
    
    /**
     * Test rights of the user and modify if he's allowed
     */
    protected function setPresentationOfficialValue($id, $value, $flashBags=true)
    {
        $presentation = $this->getPresentation($id);
        
		if($value != null) {
			// the user must own the presentation or at least be a teacher
			if($this->get('security.context')->isGranted('ROLE_TEACHER')) {
				// check if the value is correct
				$newValue = intval($value);
				if(in_array($newValue, array(0, 1))) {
					// modification
					if($presentation->getMatrix()->getOfficial()) {
						$presentation->setOfficial($newValue);
						$this->getDoctrine()->getManager()->flush();
					}
					else {
						$this->get('request')->getSession()->getFlashBag()->add('danger', 'La matrice associée n\'est pas officielle');
					}
				}
			}
			elseif($flashBags) {
				$this->get('request')->getSession()->getFlashBag()->add('danger', 'Vous ne disposez pas de droits suffisants pour effectuer cette opération');
			}
		}
    }
    
    
}
