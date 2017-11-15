<?php

namespace VMB\ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VMB\ForumBundle\Entity\Comment;

class DefaultController extends Controller
{
    public function indexAction($topic,$page)
    {   
        if ($topic == 'all'){
        $em = $this->getDoctrine()->getManager();
        $nbPerPage =4;       
         $entities = $em->getRepository('VMBForumBundle:discussion')->getTopDiscussions($nbPerPage,$page);
         $nbPages = ceil(count($entities)/$nbPerPage);
        $topics = $em->getRepository('VMBPresentationBundle:Topic')->findBylvl(0);
        $counts = $em->getRepository('VMBForumBundle:discussion')->getNbDiscussions();
          
        return $this->render('VMBForumBundle:Default:index.html.twig', array(
            'mainTitle'=> $this->get('translator')->trans('forum.browse'),
            'entities'=>$entities,
            'topics'=>$topics,
            'page'     => $page,
            'nbPerPage' => $nbPerPage,
			'nbPages'  => $nbPages,
			'counts' =>$counts
            ));
        }
        
        else {
         $em = $this->getDoctrine()->getManager();
        $nbPerPage =4;       
        $entity = $em->getRepository('VMBPresentationBundle:Topic')->findOneBytitle($topic);
         $entities = $em->getRepository('VMBForumBundle:discussion')->getTopDiscussionsByTopic($nbPerPage,$page,$entity);
         $nbPages = ceil(count($entities)/$nbPerPage);
        $topics = $em->getRepository('VMBPresentationBundle:Topic')->findBylvl(0);
        $counts = $em->getRepository('VMBForumBundle:discussion')->getNbDiscussions();
        return $this->render('VMBForumBundle:Default:index.html.twig', array(
            'mainTitle'=> $this->get('translator')->trans('forum.browse'),
            'entities'=>$entities,
            'topics'=>$topics,
            'page'     => $page,
            'nbPerPage' => $nbPerPage,
			'nbPages'  => $nbPages,
            'counts' =>$counts
            ));
        }
    }
    
   
}
