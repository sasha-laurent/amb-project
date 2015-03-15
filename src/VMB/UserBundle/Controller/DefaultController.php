<?php

namespace VMB\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VMBUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
