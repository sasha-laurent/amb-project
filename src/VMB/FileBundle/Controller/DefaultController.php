<?php

namespace VMB\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VMBFileBundle:Default:index.html.twig', array('name' => $name));
    }
}
