<?php

namespace VMB\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($type, $query)
    {
        // Controller code
        return $this->render('VMBSearchBundle:results.html.twig', $results_array);
    }
}
