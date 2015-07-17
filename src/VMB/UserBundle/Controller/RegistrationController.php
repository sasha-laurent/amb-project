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

class RegistrationController extends BaseController
{
 
	/**
	* Create and process the new users' registration form.
    * TODO:
    * - If user is authenticated redirect to homepage.
    * - Role choice / Title+links customization in the form if user is granted admin rights.
    * - Ability for the user to set his own password..
	*/
    public function registerAction(Request $request)
    {
        $has_admin_rights = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
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

        $opts = array('is_admin' => $has_admin_rights);
        $form = $formFactory->createForm(new RegistrationFormType(), $user, $opts);
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            
            $form_role = $form->getData()->getRoles();
            $role = 'ROLE_STUDENT';
            // Only admins can define a user's role
            if( isset($form_role) 
                && $has_admin_rights 
                && in_array($form_role, array('ROLE_ADMIN', 'ROLE_STUDENT', 'ROLE_TEACHER')))
            {
                    $role = $form_role;
			}

            $user->addRole($role);
            
            $tokenGenerator = $this->get('fos_user.util.token_generator');
			$password = substr($tokenGenerator->generateToken(), 0, 8); // 8 chars

            $user->setPlainPassword($password);
            $userManager->updateUser($user);
            
            $message = \Swift_Message::newInstance()
				->setSubject('Inscription à VMB')
				->setFrom('vmb@vmb.com')
				->setTo($user->getEmail())
				->setBody($this->renderView('VMBUserBundle:User:registrationMail.txt.twig', array('username' => $user->getUsername(), 'password' => $password)))
			;
			$this->get('mailer')->send($message);

            if (null === $response = $event->getResponse()) {
                if($has_admin_rights){
    				$flashMessage = $this->get('translator')->trans('admin.user.added');
    				$request->getSession()->getFlashBag()->add('success', $flashMessage);
                    $url = $this->generateUrl('admin_user');
                } else {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                }
                $response = new RedirectResponse($url);
            }

            //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            
            return $response;
        }

        $form_params = array('form' => $form->createView());
        if($has_admin_rights){
            $form_params['mainTitle'] = $this->get('translator')->trans('admin.user.add');
            $form_params['backButtonUrl'] = $this->generateUrl('admin_user');
        } else {
            $form_params['mainTitle'] = $this->get('translator')->trans('registration.register');
        }
        return $this->render('::Backend/form.html.twig', $form_params);
			
    }
}	
