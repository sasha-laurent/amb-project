<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VMBResourceBundle:Default:index.html.twig', array('name' => $name));
    }
}
