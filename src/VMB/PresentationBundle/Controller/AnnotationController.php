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
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);
        
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Presentation')));
        }
        
        $annotation = new Annotation();
        $annotation->setPresentation($entity);
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
				//$annotation->preUpload();
				$em->flush();
				//$annotation->upload();

				return $this->redirect($this->generateUrl('annotation_edit', array('id' => $id)));
			}
		}
        
        $args = array(
            'mainTitle' => $entity->getTitle(),
			'presentation' => $entity,
			'form' => $form->createView()
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
    public function deleteAction(Request $request)
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
