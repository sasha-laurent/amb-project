<?php

namespace VMB\ContextualHelpBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use VMB\ContextualHelpBundle\Entity\Help;
use VMB\ContextualHelpBundle\Form\HelpType;

/**
 * Help controller.
 *
 */
class HelpController extends Controller
{

    /**
     * Lists all Help entities.
     *
     */
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('VMBContextualHelpBundle:Help')->findAll();

        return $this->render('VMBContextualHelpBundle:Help:index.html.twig', array(
            'mainTitle' => $this->get('translator')->trans('help.main_title'),
			'addButtonUrl' => $this->generateUrl('help_new'),
            'entities' => $entities,
            'routes' => $this->container->get('router')->getRouteCollection()->all()
        ));
    }
    
    public function helpAction($route)
    {
		$em = $this->getDoctrine()->getManager();
        $help = $em->getRepository('VMBContextualHelpBundle:Help')->getHelp($route);
        
        return $this->render('VMBContextualHelpBundle:Help:modal.html.twig', array('help' => $help));
	}

    /**
     * Displays a form to create a new Help entity.
     *
     */
     
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function newAction()
    {
        $help = new Help();

        return $this->renderForm($help);
    }

    /**
     * Displays a form to edit an existing Help entity.
     *
     */
     
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function editAction($id)
    {
        $help = $this->getHelp($id);

		return $this->renderForm($help);
    }

    /**
     * Finds and displays a Help entity.
     */
    protected function renderForm($help)
    {
		$request = $this->get('request');
		
		$form = $this
			->get('form.factory')
			->create(new HelpType($this->container->get('router')->getRouteCollection()->all()), $help);
			
		if ($request->isMethod('POST')) 
		{
			$form->handleRequest($request);
			if ($form->isValid()) 
			{
				$em = $this->getDoctrine()->getManager();
				$flashMessage = ($help->getId() == null) ? $this->get('translator')->trans('help.added') : $this->get('translator')->trans('help.edited');
				$em->persist($help);
				$em->flush();

				$request->getSession()->getFlashBag()->add('success', $flashMessage);
				return $this->redirect($this->generateUrl('help'));
			}
		}

		return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => (($help->getId() == null) ? $this->get('translator')->trans('help.add') : $this->get('translator')->trans('help.edit')),
				'backButtonUrl' => $this->generateUrl('help')
			));
    }
    /**
     * Deletes a Help entity.
     *
     */
     
    /**
    * @Security("is_granted('ROLE_ADMIN')")
    */
    public function deleteAction(Request $request, $id)
    {
        $help = $this->getHelp($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($help);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', $this->get('translator')->trans('help.deleted'));
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('help'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => '"'.$help->getTitle().'"',
			'mainTitle' => $this->get('translator')->trans('help.delete'),
			'backButtonUrl' => $this->generateUrl('help')
		));
    }

    /**
     * Retrieve an existing Help entity.
     */
    protected function getHelp($id)
    {
        $help = $this->getDoctrine()->getManager()->getRepository('VMBContextualHelpBundle:Help')->find($id);

		if ($help == null) {
			throw $this->createNotFoundException('Unable to find Help entity.');
		}

		return $help;
    }
}
