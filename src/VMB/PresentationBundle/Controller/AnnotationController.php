<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function editAction(Request $request, $id, $a)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBPresentationBundle:Presentation')->findWithConcreteResources($id);
        
        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Presentation')));
        }
        
        if($this->get('security.context')->isGranted('ROLE_ADMIN') || $entity->isOwner($this->getUser())) {	
			
			$suggestion = ($request->query->get('suggestion') == 1) ? true : false;
			if($a != null) {
				$annotation = $em->getRepository('VMBPresentationBundle:Annotation')->find($a);
				if($annotation->getPresentation() == $entity) {
					$mode = 'edit';
					$suggestion = $annotation->getSuggested();
				}
				else { $a = null; }
			}
			
			if($a == null) {
				$annotation = new Annotation();
				$annotation->setPresentation($entity);
				$annotation->setSuggested($suggestion);
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
						return $this->redirect($this->generateUrl('annotation_edit', array('id' => $id, 'suggestion' => ($suggestion ? 1 : 0))));
					}
					else {
						$request->getSession()->getFlashBag()->add('danger',"An error occured");
					}
				}
			}
			
			
			$args = array(
				'backButtonUrl' => $this->generateUrl('presentation_edit', array('id' => $id)),
				'mainTitle' => ($suggestion ? '[Suggestions] ' : '') . $entity->getTitle(),
				'presentation' => $entity,
				'suggestion' => $suggestion,
				'form' => $form->createView(),
				'mode' => $mode,
				'annotation' => $annotation
			);
			return $this->render('VMBPresentationBundle:Annotation:edit.html.twig', $args);
			
		}
		else {
			throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Presentation')));
		}
    }
    
    /**
     * Deletes a Annotation entity.
     *
     */
    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function deleteAction(Request $request, $id)
    {
        $annotation = $this->getAnnotation($id);
		$presentation_id = $annotation->getPresentation()->getId();
		$suggestion = false;
		if($this->get('security.context')->isGranted('ROLE_ADMIN') || $annotation->getPresentation()->isOwner($this->getUser())) {	
			try {
				$suggestion = $annotation->getSuggested();
				$em = $this->getDoctrine()->getManager();
				$em->remove($annotation);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Annotation deleted');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('annotation_edit', array('id' => $presentation_id, 'suggestion' => ($suggestion ? 1 : 0))));
		}
		else {
			throw $this->createNotFoundException($this->get('translator')->trans('message.error.entity_not_found', array('%class%' => 'Presentation')));
		}
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
