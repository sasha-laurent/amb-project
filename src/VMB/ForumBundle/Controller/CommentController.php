<?php

namespace VMB\ForumBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VMB\ForumBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Comment controller.
 *
 */
class CommentController extends Controller
{

    /**
     * Lists all Discussion entities.
     *
     */
    public function indexAction($page)
    {   
        if ($page < 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		$nbPerPage = 5;
		$em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $entities = $em->getRepository('VMBForumBundle:Comment')->getMyComments($page, $nbPerPage, $user);
        
        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);
		
		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }
        return $this->render('VMBForumBundle:Comment:index.html.twig', array(
            'entities' 	=> $entities,
            'nbPerPage' => $nbPerPage,
			'nbPages'  	=> $nbPages,
			'page'     	=> $page
        ));
        }    
}
