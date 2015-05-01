<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VMBPresentationBundle:Presentation')->findAll();

        return $this->render('VMBPresentationBundle:Presentation:index.html.twig', array(
            'mainTitle' => 'Toutes les présentations',
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
				
				$postValues = $request->request->all();
				
				$checkedRes = $presentation->getResources();
				$indexedCheckedRes = array();
				if($checkedRes != null) {
					foreach($checkedRes as $r) {
						$indexedCheckedRes[$r->getId()] = $r;
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
							$newCheckedRes->setUsedResource($em->getRepository('VMBPresentationBundle:UsedResource')->find($resId));
							$newCheckedRes->setSort($position);
							$em->persist($newCheckedRes);
						}
					}
				}
				
				// If there are still values in this array it means it has to be removed
				foreach($indexedCheckedRes as $r) {
					$em->remove($r);
				}
				
				$em->persist($presentation);
				$em->flush();

				$flashMessage = !$presentation->toString() ? 'Présentation ajoutée' : 'Présentation modifiée';
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				
				return $this->redirect($this->generateUrl('presentation'));
			}
		}

		return $this->render('VMBPresentationBundle:Presentation:edit.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => ((!($presentation->toString())) ? 'Ajout d\'une présentation' : 'Modification d\'une présentation '.$presentation->toString()),
				'backButtonUrl' => $this->generateUrl('presentation'),
				'matrix' => $presentation->getMatrix(),
				'presentation' => $presentation
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
			'entityTitle' => '"'.$presentation->toString().'"',
			'mainTitle' => 'Suppression de la présentation '.$presentation->toString(),
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
