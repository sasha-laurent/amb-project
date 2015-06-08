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
            'mainTitle' => $this->get('translator')->trans('resource.browse'),
			'addButtonUrl' => $this->generateUrl('resource_new'),
            'entities' => $entities
        ));
    }
    
    public function browseAction($page, $topic=null)
    {
		if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		$nbPerPage = 12;
		$em = $this->getDoctrine()->getManager();
		$topics = $em->getRepository('VMBPresentationBundle:Topic')->childrenHierarchy();
		
		
		$mainTitle = $this->get('translator')->trans('resource.browse');
		if($topic != null) {
			$topic = $em->getRepository('VMBPresentationBundle:Topic')->find($topic);
			$mainTitle = $topic->getTitle().' - '.$this->get('translator')->trans('menu.resources');
		}
		
		$request = $this->get('request');
		$official = ($request->query->get('official') == 1) ? true : 'all';
		$personal = ($request->query->get('personal') == 1);
		$search = $request->query->get('search');
		
        $entities = $em->getRepository('VMBResourceBundle:Resource')->getResources($page, $nbPerPage, $topic, $official, ($personal ? $this->getUser() : null), $search);
        
        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);
		
		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBResourceBundle:Resource:browseTopic.html.twig', array(
            'mainTitle' => $mainTitle,
            'topic' 	=> $topic,
            'topics' 	=> $topics,
            'search' 	=> $search,
            'entities' 	=> $entities,
            'nbPerPage' => $nbPerPage,
			'nbPages'  	=> $nbPages,
			'page'     	=> $page
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
        
        $request = $this->get('request');
		$official = ($request->query->get('official') == 1) ? true : 'all';
		$personal = ($request->query->get('personal') == 1);
		$search = $request->query->get('search');
		$topic = $request->query->get('topic');
		if($topic != null) {
			$topic = $em->getRepository('VMBPresentationBundle:Topic')->find($topic);
		}
		
		$prev = $next = null;
		$position = intval($request->query->get('position'));
		if($position > 0) {
			if($position > 1) {
				$prev = $em->getRepository('VMBResourceBundle:Resource')->getResources($position - 1, 1, $topic, $official, ($personal ? $this->getUser() : null), $search);
			}
			$next = $em->getRepository('VMBResourceBundle:Resource')->getResources($position + 1, 1, $topic, $official, ($personal ? $this->getUser() : null), $search);
		}
		

        return $this->render('VMBResourceBundle:Resource:show.html.twig', array(
            'entity'      => $entity,
            'mainTitle' => $entity->getTitle(),
            'saveToCaddy' => 'resource',
            'inCaddy' => $this->getUser()->resourceIsInCaddy($entity),
            'prev' => $prev,
            'next' => $next,
            'position' => $position
        ));
    }
    
    /**
     * Search an entity.
     *
     */
    public function searchAction()
    {
		$request = $this->container->get('request');

		if($request->isXmlHttpRequest())
		{
			$motcle = '';
			$motcle = $request->request->get('motcle');
        
			$em = $this->getDoctrine()->getManager();

			if($motcle != '')
			{
				$results = $em->getRepository('VMBResourceBundle:Resource')->findByKeyword($motcle);
			}

			return $this->render('VMBResourceBundle:Resource:searchResult.html.twig', array(
				'results' => $results
				));
		}
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
				
				$request->getSession()->getFlashBag()->add('success', $this->get('translator')->trans('resource.deleted'));
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('resource'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => '"'.$resource->getTitle().'"',
			'mainTitle' => $this->get('translator')->trans('resource.delete'),
			'backButtonUrl' => $this->container->get('vmb_presentation.previous_url')->getPreviousUrl($request, $this->generateUrl('resource'))
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
