<?php
namespace VMB\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use VMB\UserBundle\Form\Type\RegistrationFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

    /**
    * Create and process the new users' registration form.
    * TODO:
    * - If the user is already authenticated, redirect him/her to the homepage.
    */
    
class RegistrationController extends BaseController
{

    public function registerAction(Request $request)
    {
        $has_admin_rights = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createBuilder(new RegistrationFormType(), $user, array('is_admin' => $has_admin_rights))->getForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            
            /*
             * Role Definition
             * Only admins can define a user's role
            **/

            $form_role = $form->getData()->getRoles();
            if( isset($form_role) 
                && $has_admin_rights 
                && in_array($form_role, array('ROLE_ADMIN', 'ROLE_STUDENT', 'ROLE_TEACHER')))
            {
                    $role = $form_role;
			} else {
                $role = 'ROLE_STUDENT';
            }
            $user->addRole($role);
            
            /*
             * Password auto-generation IF Admin created the account
            **/
            if($has_admin_rights)
            {
                $tokenGenerator = $this->get('fos_user.util.token_generator');
    			$password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars
                $user->setPlainPassword($password);
            }
            try {
                $userManager->updateUser($user);
            } catch(\Doctrine\DBAL\DBALException $e){
                // If there is a duplicate entry for username 
                // then say username not available
                $flashMessage = $this->get('translator')->trans('registration.existing_username', array(), 'FOSUserBundle');
                $request->getSession()->getFlashBag()->add('danger', $flashMessage);
                $url = $this->generateUrl('fos_user_registration_register');
                return new RedirectResponse($url);
            }

            /*
             * Sending registration mail 
            **/
            $message_opts = array('username' => $user->getUsername());
            if($has_admin_rights){
                $message_opts['password'] = $password;
            }
            // Translate email subject?
            $message = \Swift_Message::newInstance()
				->setSubject('Inscription à AMB')
				->setFrom('no-reply@edu3d.enstb.org')
				->setTo($user->getEmail())
				->setBody($this->renderView('VMBUserBundle:User:registrationMail.txt.twig',$message_opts))
            ;
			$this->get('mailer')->send($message);


            /*
             * User redirect logic
             * TODO: Doesn't work for admin panel - probably user session gets refreshed without admin rights.
            **/
            if (null === $response = $event->getResponse()) {
                if($has_admin_rights){
    				$flashMessage = $this->get('translator')->trans('admin.user.added');
    				$request->getSession()->getFlashBag()->add('success', $flashMessage);
                    $url = $this->generateUrl('admin_user');
                } else {
                    // $this->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                    // $url = $this->generateUrl('fos_user_registration_check_email');
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                }
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        $form_params = array('form' => $form->createView());
        if($has_admin_rights){
            $form_params['mainTitle'] = $this->get('translator')->trans('admin.user.add');
            $form_params['backButtonUrl'] = $this->generateUrl('admin_user');
        } else {
            $form_params['mainTitle'] = $this->get('translator')->trans('registration.register', array(), 'FOSUserBundle');
        }
        return $this->render('::Backend/form.html.twig', $form_params);
			
    }
}	
