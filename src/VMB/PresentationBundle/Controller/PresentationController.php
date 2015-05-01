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
		$nbPerPage = 6;

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
     * Finds and displays a Presentation entity.
     *
     */
    /**
    * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Presentation entity.');
        }

        return $this->render('VMBPresentationBundle:Presentation:show.html.twig', array(
            'mainTitle' => $entity->getTitle(),
			'entity' => $entity
        ));
    }

    /**
     * Displays a form to create a new Presentation entity.
     *
     */
    /**
    * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
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
    * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function editAction($id)
    {
        $presentation = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Presentation')->findWithSortedResources($id);

		return $this->renderForm($presentation);
    }

    /**
     * Finds and displays a Presentation entity.
     */
    /**
    * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
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
				
				$postValues = $request->request->all();
				
				$checkedRes = $presentation->getResources();
				$indexedCheckedRes = array();
				if($checkedRes != null) {
					foreach($checkedRes as $r) {
						$indexedCheckedRes[$r->getUsedResource()->getId()] = $r;
					}
				}
				
				foreach($postValues as $key => $position) {
					if(preg_match('`sort_([0-9]+)`', $key, $matches)) {
						$resId = intval($matches[1]);
						
						// If the resource has already been persisted as a checked resource
						if(isset($indexedCheckedRes[$resId])) {
							$indexedCheckedRes[$resId]->setSort($position);
							unset($indexedCheckedRes[$resId]);
						}
						else {
							$newCheckedRes = new CheckedResource();
							$newCheckedRes->setPresentation($presentation);
							$presentation->addResource($newCheckedRes);
							$newCheckedRes->setUsedResource($em->getRepository('VMBPresentationBundle:UsedResource')->find($resId));
							$newCheckedRes->setSort($position);
							$em->persist($newCheckedRes);
						}
					}
				}
				
				// If there are still values in this array it means it has to be removed
				foreach($indexedCheckedRes as $r) {
					$presentation->removeResource($r);
					$em->remove($r);
				}
				
				$em->persist($presentation);
				$em->flush();

				$flashMessage = !$presentation->toString() ? 'Présentation ajoutée' : 'Présentation modifiée';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				
			}
		}

		return $this->render('VMBPresentationBundle:Presentation:edit.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($presentation->toString())) ? 'Ajout d\'une présentation' : $presentation->toString()),
				'backButtonUrl' => $this->generateUrl('presentation'),
				'matrix' => $presentation->getMatrix(),
				'presentation' => $presentation,
				'alertDismissible' => true
			));
    }
    /**
     * Deletes a Presentation entity.
     *
     */
    /**
    * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
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
    * @Security("has_role('IS_AUTHENTICATED_REMEMBERED')")
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
