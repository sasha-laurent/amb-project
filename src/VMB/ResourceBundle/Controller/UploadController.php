<?php

namespace VMB\ResourceBundle\Controller;

use Symfony\Component\Validator\Constraints as Assert;  
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    /*
     *TODO: 
     * Log $e->getMessage() in $this->get('logger')
     * with some customization
    */
    private function exceptionManager($request, $e){
        $err_str = $this->get('translator')->trans('resource.upload_error');
        if($request->isXmlHttpRequest()){ 
            return new JsonResponse(array('success' => false, 
                'code' => 500,
                'message' => $err_str));
            // Could output whole stack trace for exception '.$e->__toString())
        } else {
            $request->getSession()->getFlashBag()->add('error', $err_str);

            return $this->redirect($this->generateUrl('resource'));
        }
    }

    protected function renderForm($resource, $is_modal_dialog = false)
    {
        $request = $this->get('request');
        $isTeacher = $this->get('security.authorization_checker')->isGranted('ROLE_TEACHER');
        $form = $this
            ->get('form.factory')
            ->create(new ResourceType($isTeacher), $resource);
        // Record whether Resource.id is already set
        $is_new = ($resource->getId() == null);
            
        if ($request->isMethod('POST')) 
        {  
            $form->handleRequest($request);
            
            if ($form->isValid()) 
            {
                // if the resource has a quiz, then we create the questions.
                if(null !== $resource->getQuiz()){
                    foreach($resource->getQuiz()->getMultichoices() as $multichoice)
                    {
                        $multichoice->setQuiz($resource->getQuiz());
                        if ($multichoice->getHint() == null);
                        {
                            $multichoice->setHint("Pas d'indice");
                        }
                        foreach($multichoice->getPropositions() as $proposition)
                        {
                            $proposition->setMultichoice($multichoice);
                        }
                    }
                    foreach($resource->getQuiz()->getSinglechoices() as $singlechoice)
                    {
                        $singlechoice->setQuiz($resource->getQuiz());
                        if ($singlechoice->getHint() == null)
                            $singlechoice->setHint("Pas d'indice");
                        foreach($singlechoice->getPropositions() as $proposition)
                        {
                            $proposition->setSinglechoice($singlechoice);
                        }
                    }
                    foreach($resource->getQuiz()->getTextareas() as $textarea)
                    {
                        $textarea->setQuiz($resource->getQuiz());

                        if ($textarea->getHint() == null)
                            $textarea->setHint("Pas d'indice");
                    }
                    foreach($resource->getQuiz()->getNumericalvalues() as $numericalvalue)
                    {
                        $numericalvalue->setQuiz($resource->getQuiz());
                        if($numericalvalue->getHint() == null)
                            $numericalvalue->setHint("Pas d'indice");
                    }
                }
                $keepThumbs = $request->request->get('keepThumbs');
                if(isset($keepThumbs) 
                    && intval($keepThumbs) == 0)
                { // Delete previous custom art
                    $resource->removeCustomArt();
                }
                try{
                    $flashMessage =  $is_new ?
                        $this->get('translator')->trans('resource.added') : $this->get('translator')->trans('resource.modified');

                    if(null !== $resource->customAudioArt)
                    {
                        $resource->persistCustomAudioArt();
                    }
                    
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($resource);
                    $em->flush();

					if($request->isXmlHttpRequest())
                    { 
                    // Return new resource entity as JSON Object 
                    // so it can be used immediately 
                    $res = new JsonResponse(
                    	array('success' => true, 
                    		'message' => $this->get('translator')->trans('resource.added'),
                            'rid' => $resource->getId(),
                    		'resource' => array(
                    			'title' => $resource->getTitle(),
                    			'src' => "/".$resource->getThumbsPath(),
                                'type'=> $resource->getType()
                    			)));
                    return $res;
	                } else {
	                    $request->getSession()->getFlashBag()->add('success', $flashMessage);

	                    return $this->redirect($this->generateUrl('resource'));
	                }
                } catch (\Doctrine\ORM\ORMException $e){
                    $this->exceptionManager($request, $e);
                } catch (\IOException $e) {
                    $this->exceptionManager($request, $e);
                } catch (\FileException $e) {
                    $this->exceptionManager($request, $e);
                } catch(\Exception $e){
                    $this->exceptionManager($request, $e);
                }
            } else { // if a form is not submitted (submit button pressed) it is considered invalid
                if($request->isXmlHttpRequest()){ 
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
            'mainTitle' => ($is_new ? 
                $this->get('translator')->trans('resource.add') : $this->get('translator')->trans('resource.edit')),
            'resource' => $resource);

        if($is_modal_dialog)
        {
            $render_opts['is_modal'] = true;
        } elseif(!$is_modal_dialog && !$is_new)
        { // logix fun: this cond === !(is_modal || is_new)
            $render_opts['backButtonUrl'] = $this->get('vmb_presentation.previous_url')
            ->getPreviousUrl($request, $this->generateUrl('resource'));           
        }

        return $this->render('VMBResourceBundle:Upload:form.html.twig', $render_opts); 
    }

    public function newAction(Request $request, $is_modal = false)
    {
        $resource = new Resource();
        $resource->setOwner($this->getUser());
        return $this->renderForm($resource, $is_modal);
    }

    public function editAction(Request $request, $id)
    {
        try {
            $resource = $this->getResource($id);
            return $this->renderForm($resource);
        } catch(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e){
            $request->getSession()->getFlashBag()
                ->add('error', $e->getMessage());
            return $this->redirect(
                $this->container->get('vmb_presentation.previous_url')
            ->getPreviousUrl($request, $this->generateUrl('resource')));
        }
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
