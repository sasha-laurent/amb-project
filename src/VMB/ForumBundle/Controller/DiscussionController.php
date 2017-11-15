<?php

namespace VMB\ForumBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VMB\ForumBundle\Form\discussionType;
use VMB\ForumBundle\Form\CommentType;
use VMB\ForumBundle\Entity\discussion;
use VMB\ForumBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Discussion controller.
 *
 */
class DiscussionController extends Controller
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
		
		$nbPerPage = 4;
		$em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entities = $em->getRepository('VMBForumBundle:discussion')->getDiscussions($page, $nbPerPage, $user);
        
        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
		$nbPages = ceil(count($entities)/$nbPerPage);
		
		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}

        return $this->render('VMBForumBundle:Discussion:browse.html.twig', array(
            'mainTitle'=> $this->get('translator')->trans('forum.browse'),
            'entities' 	=> $entities,
            'nbPerPage' => $nbPerPage,
			'nbPages'  	=> $nbPages,
			'page'     	=> $page
        ));
    }    
    

    /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function showAction(Request $request, $id, $page)
    {   
        if ($page < 1) {
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            }
        $em = $this->getDoctrine()->getManager();
		$nbPerPage = 10;
        $entity = $em->getRepository('VMBForumBundle:discussion')->find($id);
        $entities = $em->getRepository('VMBForumBundle:Comment')->getComments($page, $nbPerPage, $entity);
 

        $nbPages = ceil(count($entities)/$nbPerPage);
        if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
        
        $comment = new Comment();
        $form = $this->get('form.factory')->createBuilder(new CommentType, $comment); 
        $user = $this->getUser();
        
        $comment->setUser($user);
        $entity->addComment($comment);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find discussion entity.');
        }
        
		
        if ($form->getForm()->handleRequest($request)->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->persist($comment);
           $em->flush();
           $newform = $this->get('form.factory')->createBuilder(new CommentType); 
           $listComments = $em->getRepository('VMBForumBundle:Comment')->findBy(array('discussion'=>$entity));
           $rend_args = array(
            'entity'      => $entity,
            'mainTitle' => $entity->getTitle(),
            'listComments' => $listComments,
            'form' => $newform->getForm()->createView(),
            'page'=>$page,
            'nbPerPage' => $nbPerPage,
			'nbPages'  	=> $nbPages,
            );
           return $this->render('VMBForumBundle:Discussion:show.html.twig', $rend_args);
           }
           $rend_args = array(
            'entity'      => $entity,
            'listComments' => $entities,
            'form' => $form->getForm()->createView(),
            'page'=>$page,
            'nbPerPage' => $nbPerPage,
			'nbPages'  	=> $nbPages,
        );  
         return $this->render('VMBForumBundle:Discussion:show.html.twig', $rend_args);  
       
    }
    
     /**
    * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
    */
    public function profileAction(Request $request, $id, $page)
    {   
        if ($page < 1) {
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            }
        $em = $this->getDoctrine()->getManager();
		$nbPerPage = 10;
        $user = $em->getRepository('VMBUserBundle:User')->find($id);
        $listDiscussions = $em->getRepository('VMBForumBundle:discussion')->getDiscussions($page, $nbPerPage, $user);
 

        $nbPages = ceil(count($listDiscussions)/$nbPerPage);
        if ($page > $nbPages && $page != 1) {
			throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
        
        
        if (!$user) {
            throw $this->createNotFoundException('Unable to find user entity.');
        }
              
           
        return $this->render('VMBForumBundle:Discussion:profile.html.twig', array(
            'mainTitle' => $user->getUsername(),
            'listDiscussions' => $listDiscussions,
            'nbPerPage' => $nbPerPage,
			'nbPages'  	=> $nbPages,
			'page'     	=> $page
        ));
       
    }
    
     /**
     * Add a Discussion entity.
     *
     */
    public function addAction(Request $request)
    {   

       $discussion = new discussion();
       $form = $this->get('form.factory')->createBuilder(new discussionType, $discussion); 
        
        $user = $this->getUser();
        $discussion->setUser($user);
        
        
         if ($form->getForm()->handleRequest($request)->isValid()) {
          $em = $this->getDoctrine()->getManager();
          $em->persist($discussion);
          $em->flush();
          return $this->redirect($this->generateUrl('discussion_show', array('id' => $discussion->getId())));
          }
        return $this->render('VMBForumBundle:Discussion:add.html.twig',array(
        'form' => $form->getForm()->createView()) );
    
    }
     
     
     public function validateAction(Request $request)
     {      
           $id = $request->query->get('id');
           $em = $this->getDoctrine()->getManager();
           $discussion = $em->getRepository('VMBForumBundle:discussion')->find($id);
            if (!$discussion) {
                throw $this->createNotFoundException('Unable to find UserRole entity.');
            }
 
           $discussion->setState(true);
           $em->persist($discussion);
           $em->flush();
           
        return $this->redirect($this->generateUrl('vmb_forum_my_discussions'));
     }


}
