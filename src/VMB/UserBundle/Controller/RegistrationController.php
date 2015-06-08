<?php
namespace VMB\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RegistrationController extends BaseController
{
	
	/**
	* @Security("has_role('ROLE_ADMIN')")
	*/
    public function registerAction(Request $request)
    {
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

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $role = $this->get('request')->request->get('uniqueRole');
            if(!in_array($role, array('ROLE_ADMIN', 'ROLE_STUDENT', 'ROLE_TEACHER'))) {
				$role = 'ROLE_STUDENT';
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
				$flashMessage = $this->get('translator')->trans('admin.user.added');
				$request->getSession()->getFlashBag()->add('success', $flashMessage);
                return $this->redirect($this->generateUrl('admin_user'));
            }

            //$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('::Backend/form.html.twig', 
			array(
				'form' => $form->createView(),
				'mainTitle' => $this->get('translator')->trans('admin.user.add'),
				'backButtonUrl' => $this->generateUrl('admin_user')
			));
    }
}	
