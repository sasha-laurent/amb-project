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
    public function indexAction(Request $request)
    {
    	$query = $request->query->get("query");
    	if(null === $query or empty($query)){
    		return $this->redirectToRoute("vmb_presentation_browse", 
    							$request->query->all());
    	}

    	$em = $this->getDoctrine()->getManager();
    	$usr = $this->getUser();
        
        /*
         * Results
        **/
    	$pres_repo = $em->getRepository('VMBPresentationBundle:Presentation');
    	$pres_results = $pres_repo->searchWithQuery($query, $usr);

    	$ress_repo = $em->getRepository('VMBResourceBundle:Resource');
    	$ress_results = $ress_repo->searchWithQuery($query, $usr);

        $render_opts = array(
        	'query' => $query,
        	'backButtonUrl' => $this->get('vmb_presentation.previous_url')->getPreviousUrl($request),
        	'presentations' => $pres_results,
        	'resources' => $ress_results);
        return $this->render('VMBSearchBundle:Search:results.html.twig', 
        			$render_opts);
    }
}
