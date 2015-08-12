<?php

namespace VMB\SearchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SearchController extends Controller
{
	/**
	 *
	 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
	**/
    public function indexAction(Request $request, $type, $query, $page=1)
    {

        /*
         *
         * Results + Counts 
         *
        **/
        $render_opts = array(
        	'backButtonUrl' => $this->get('vmb_presentation.previous_url')->getPreviousUrl($request),
        	'nbPages' => 1,
        	'page', $page);
        return $this->render('VMBSearchBundle:Search:results.html.twig', $render_opts);
    }
}
