<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\PresentationBundle\Entity\Presentation;
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
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        
        if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// Ici je fixe le nombre d'annonces par page à 3
		// Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
		$nbPerPage = 12;

		// On récupère notre objet Paginator
        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->getPresentations($page, $nbPerPage);

		// On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);

		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBPresentationBundle:Presentation:index.html.twig', array(
            'mainTitle'=> 'Présentations déjà existantes',
            'entities' => $entities,
			'nbPages'  => $nbPages,
			'page'     => $page
        ));
    }
    
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function personalIndexAction($page)
    {
        $em = $this->getDoctrine()->getManager();

        
        if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

		// Ici je fixe le nombre d'annonces par page à 3
		// Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
		$nbPerPage = 12;

		// On récupère notre objet Paginator
        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->getPresentations($page, $nbPerPage, 'all', 'all', $this->getUser());
        $request = $this->get('request');
		if ($request->isMethod('GET')) 
		{
			$idSwitch = $request->query->get('switch');
			if($idSwitch != null && is_numeric($idSwitch)) {
				foreach($entities as $presentation) {
					if($presentation->getId() == $idSwitch) {
						$presentation->setPublic(!$presentation->getPublic());
						$em->flush();
						break;
					}
				}
			}
		}

		// On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);

		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBPresentationBundle:Presentation:personalIndex.html.twig', array(
            'mainTitle'=> 'Mes présentations',
            'entities' => $entities,
			'nbPages'  => $nbPages,
			'page'     => $page
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
				if(preg_match('`usedResource_([0-9]+)`', $key, $matches)) {
					$resId = intval($matches[1]);
					$newRes = $matrix->getUsedResourceById($resId);
					if($newRes != null) {
						$alternativeResources[] = $newRes;
					}
				}
			}
		}

        return $this->render('VMBPresentationBundle:Presentation:show.html.twig', array(
            'mainTitle' => $entity->getTitle(),
			'entity' => $entity,
			'alternativeResources' => $alternativeResources
        ));
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
			elseif($nbResTotal == 0) {
				unset($sortedResources[$pov]);
			}
		}
		
		$additionalResourcesId = array();
		while(count($sortedResources) > 0) {
			foreach($sortedResources as $pov => $subArr) {
				foreach($subArr as $lvl => $usedRes) {
					// We add the first element we find and break
					$additionalResourcesId[] = $sortedResources[$pov][$lvl]->getId();
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

		return $this->renderForm($presentation);
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

		return $this->renderForm($presentation, true);
    }

    /**
     * Finds and displays a Presentation entity.
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    protected function renderForm($presentation, $saveAsCopy = false)
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
				
				$postValues = $request->request->all();
				
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
					if(preg_match('`(usedResource|suggested)_([0-9]+)`', $key, $matches)) {
						$resId = intval($matches[2]);
						$isSuggested = ($matches[1] == 'suggested');
						
						// If the resource has already been persisted as a checked resource
						if(!$saveAsCopy && isset($indexedCheckedRes[$resId])) {
							$indexedCheckedRes[$resId]->setSort($position);
							$indexedCheckedRes[$resId]->setSuggested($isSuggested);
							if(!$isSuggested) {
								$totalDuration += $indexedCheckedRes[$resId]->getUsedResource()->getResource()->getDuration();
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
							if(!$isSuggested) {
								$totalDuration += $newCheckedRes->getUsedResource()->getResource()->getDuration();
							}
							$newCheckedRes->setSort($position);
							$newCheckedRes->setSuggested($isSuggested);
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
				$workingPresentation->setOwner($this->getUser());
				$em->persist($workingPresentation);
				$em->flush();

				$flashMessage = !$workingPresentation->toString() ? 'Présentation ajoutée' : 'Présentation modifiée';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				
				if($saveAsCopy) {
					return $this->redirect($this->generateUrl('presentation_perso'));
				}
				
			}
		}
		
		$shownPresentation = (isset($workingPresentation)) ? $workingPresentation : $presentation;

		return $this->render('VMBPresentationBundle:Presentation:edit.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($presentation->toString())) ? 'Ajout d\'une présentation' : ($saveAsCopy ? 'Copie de ': '').$presentation->toString()),
				'backButtonUrl' => $this->generateUrl('presentation'),
				'copy' => $saveAsCopy,
				'matrix' => $presentation->getMatrix(),
				'presentation' => $shownPresentation,
				'alertDismissible' => true
			));
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
			'entityTitle' => '"'.$presentation->toString().'"',
			'mainTitle' => 'Suppression de la présentation '.$presentation->toString(),
			'backButtonUrl' => $this->generateUrl('presentation')
		));
    }

    /**
     * Retrieve an existing Presentation entity.
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
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
