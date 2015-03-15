<?php

namespace VMB\PresentationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VMBPresentationBundle:Default:index.html.twig', array('name' => $name));
    }
}
