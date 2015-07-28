<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\PresentationBundle\Entity\Annotation;
use VMB\PresentationBundle\Form\AnnotationType;

/**
 * Annotation controller.
 *
 */
class AnnotationController extends Controller
{

    /**
     * Displays a form to edit all annotations associated to an entity
     *
     */
    public function editAction(Request $request, $id, $a)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);
        
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Presentation')));
        }
        
        if($a != null) {
			$annotation = $em->getRepository('VMBPresentationBundle:Annotation')->find($a);
			if($annotation->getPresentation() == $entity) {
				$mode = 'edit';
			}
			else { $a = null; }
		}
		
		if($a == null) {
			$annotation = new Annotation();
			$annotation->setPresentation($entity);
			$mode = 'new';
		}
		
        $form = $this
			->get('form.factory')
			->create(new AnnotationType(), $annotation);
		
        
        if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$em->persist($annotation);
				if($annotation->preUpload()) {
					$em->flush();
					$annotation->upload();
					return $this->redirect($this->generateUrl('annotation_edit', array('id' => $id)));
				}
				else {
					$request->getSession()->getFlashBag()->add('danger',"An error occured");
				}
			}
		}
        
        $args = array(
			'backButtonUrl' => $this->generateUrl('presentation_edit', array('id' => $id)),
            'mainTitle' => $entity->getTitle(),
			'presentation' => $entity,
			'form' => $form->createView(),
			'mode' => $mode,
			'annotation' => $annotation
        );
        return $this->render('VMBPresentationBundle:Annotation:edit.html.twig', $args);
    }

    /**
     * Deletes a Annotation entity.
     *
     */
    public function addAction(Request $request)
    {
        $annotation = $this->getAnnotation($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($annotation);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Annotation deleted');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('annotation'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'le annotation "'.$annotation->toString().'"',
			'mainTitle' => 'Suppression du annotation '.$annotation->toString(),
			'backButtonUrl' => $this->generateUrl('annotation')
		));
    }

    
    /**
     * Deletes a Annotation entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $annotation = $this->getAnnotation($id);
		$presentation_id = $annotation->getPresentation()->getId();
		try {
			$em = $this->getDoctrine()->getManager();
			$em->remove($annotation);
			$em->flush();
			
			$request->getSession()->getFlashBag()->add('success', 'Annotation deleted');
		} catch (\Exception $e) {
			$request->getSession()->getFlashBag()->add('danger',"An error occured");
		}
		return $this->redirect($this->generateUrl('annotation_edit', array('id' => $presentation_id)));
    }

    /**
     * Retrieve an existing Annotation entity.
     */
    protected function getAnnotation($id)
    {
        $annotation = $this->getDoctrine()->getManager()->getRepository('VMBPresentationBundle:Annotation')->find($id);

		if ($annotation == null) {
			throw $this->createNotFoundException('Unable to find Annotation entity.');
		}

		return $annotation;
    }
}
