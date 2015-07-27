<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Component\Validator\Constraints as Assert;  
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Form;

use VMB\ResourceBundle\Entity\Resource;
use VMB\ResourceBundle\Form\ResourceType;

class UploadController extends Controller
{
    
    protected function renderForm($resource, $is_modal_dialog = false)
    {
        $request = $this->get('request');
        $form = $this
            ->get('form.factory')
            ->create(new ResourceType(), $resource);
            
        if ($request->isMethod('POST')) 
        {
            $form->handleRequest($request);
            if ($form->isValid()) 
            {
                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($resource);
                    $em->flush();   
                } catch (\Doctrine\ORM\ORMException $e){
                    $err_str = $this->get('translator')
                        ->trans('resource.upload_error');
                     if($request->isXmlHttpRequest()){       
                        return new JsonResponse(array('success' => false, 
                                                      'code' => 500,
                                                      'message' => $err_str));
                    } else {
                        $request->getSession()->getFlashBag()->add('error', $err_str);

                        return $this->redirect($this->generateUrl('resource'));
                    }        
                } catch(\Exception $e){
                    // TODO: 
                    // Log $e->getMessage() in $this->get('logger')
                    // with some customization
                }

                if($request->isXmlHttpRequest()){ 
                    // Return new resource entity to be inserted in File List available immediately
                    $res = new JsonResponse(
                    	array('success' => true, 
                    		'message' => $this->get('translator')->trans('resource.added'),
                    		'resource' => array('res_id' => $resource->getId(),
                    			'res_title' => $resource->getTitle(),
                    			'res_thumb_path' => $resource->getThumbsPath()
                    			)));
                    return $res;
                } else {
                    $flashMessage = ($resource->getId() == null) ? 
                    $this->get('translator')->trans('resource.added') : $this->get('translator')->trans('resource.modified');
                    $request->getSession()->getFlashBag()->add('success', $flashMessage);

                    return $this->redirect($this->generateUrl('resource'));
                }
            } else {
                if($request->isXmlHttpRequest()){ 
                    // if a form is not submitted (submit button pressed) it is considered invalid
                    //$err_str = (string) $form->getErrors(true); //debug
                    $err_str = $this->get('translator')->trans('resource.upload_error');
                    return new JsonResponse(array('success' => false, 
                                                  'code' => 400,
                                                  'message' => $err_str));
                }
            }
        }

        $render_opts = array(
            'form' => $form->createView(),
            'mainTitle' => (($resource->getId() == null) ? 
                $this->get('translator')->trans('resource.add') : $this->get('translator')->trans('resource.edit')));

        if($is_modal_dialog){
            return $this->render('VMBResourceBundle:Upload:form.html.twig', $render_opts);            
        } else {
            $render_opts['backButtonUrl'] = $this->container->get('vmb_presentation.previous_url')
            ->getPreviousUrl($request, $this->generateUrl('resource'));
            return $this->render('::Backend/form.html.twig', $render_opts);
        }
    }

    public function newAction(Request $request, $is_modal = false)
    {
        $resource = new Resource();
        $resource->setOwner($this->getUser());
        return $this->renderForm($resource, $is_modal);
    }

    public function editAction($id)
    {
		$resource = $this->getResource($id);

		return $this->renderForm($resource);
    }    
    
    /**
     * Retrieve an existing Resource entity.
     */
    protected function getResource($id)
    {
        $resource = $this->getDoctrine()->getManager()
        ->getRepository('VMBResourceBundle:Resource')->find($id);

		if ($resource == null) {
			throw $this->createNotFoundException('Unable to find Resource entity.');
		}

		return $resource;
    }
}
