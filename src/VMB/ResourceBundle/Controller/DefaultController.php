<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VMB\ResourceBundle\Entity\Resource;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
		$resource = new Resource();
        $name = 'name';

        return $this->render('VMBResourceBundle:Default:index.html.twig', array('name' => $name));
 // return $this->render('VMBPresentationBundle:Default:index.html.twig', array('name' => $name));
    }
}
