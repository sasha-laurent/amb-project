<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\ResourceBundle\Entity\Resource;
use VMB\ResourceBundle\Form\ResourceType;

/**
 * Resource controller.
 *
 */
class ResourceController extends Controller
{

    /**
     * Lists all Resource entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VMBResourceBundle:Resource')->findAll();

        return $this->render('VMBResourceBundle:Resource:index.html.twig', array(
            'mainTitle' => 'Parcourir les ressources',
			'addButtonUrl' => $this->generateUrl('resource_new'),
            'entities' => $entities
        ));
    }

    /**
     * Finds and displays a Resource entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('VMBResourceBundle:Resource')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Resource entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('VMBResourceBundle:Resource:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Resource entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $resource = $this->getResource($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($resource);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'Ressource supprimée');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('resource'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => 'la ressource "'.$resource->getTitle().'"',
			'mainTitle' => 'Suppression d\'une ressource '.$resource->getTitle(),
			'backButtonUrl' => $this->generateUrl('resource')
		));
    }

    /**
     * Retrieve an existing Resource entity.
     */
    protected function getResource($id)
    {
        $resource = $this->getDoctrine()->getManager()->getRepository('VMBResourceBundle:Resource')->find($id);

		if ($resource == null) {
			throw $this->createNotFoundException('Unable to find Resource entity.');
		}

		return $resource;
    }
}
