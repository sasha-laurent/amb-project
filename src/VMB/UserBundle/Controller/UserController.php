<?php

namespace VMB\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use VMB\UserBundle\Entity\User;
use VMB\UserBundle\Form\UserType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        if($request->isMethod('GET')){
			
			$id = $request->query->get('id');
			
			if($id != null){
				
				$role = $request->query->get('role');
				
				$user = $this->getDoctrine()->getManager()->getRepository('VMBUserBundle:User')->find($id);
				
				
				$roles = array('ROLE_STUDENT', 'ROLE_ADMIN', 'ROLE_TEACHER');
				foreach($roles as $r) {
					$user->removeRole($r);
				}
				
				if(is_numeric($id) && in_array($role, $roles)) {
					$user->addRole($role);
				}
				$em->persist($user);
				$em->flush();
			}
		}
        $entities = $em->getRepository('VMBUserBundle:User')->findAll();

        return $this->render('VMBUserBundle:User:index.html.twig', array(
            'mainTitle' => $this->get('translator')->trans('admin.user.manage'),
			'addButtonUrl' => $this->generateUrl('fos_user_registration_register'),
            'entities' => $entities
        ));
        
    }

    /**
     * Deletes a User entity.
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $user = $this->getUserById($id);

		if ($request->isMethod('POST')) {
			try {
				$em = $this->getDoctrine()->getManager();
				$em->remove($user);
				$em->flush();
				
				$request->getSession()->getFlashBag()->add('success', 'User deleted');
			} catch (\Exception $e) {
				$request->getSession()->getFlashBag()->add('danger',"An error occured");
			}
			return $this->redirect($this->generateUrl('admin_user'));
		}

		// Si la requête est en GET, on affiche une page de confirmation avant de delete
		return $this->render('::Backend/delete.html.twig', array(
			'entityTitle' => $this->get('translator')->trans('admin.user').' "'.$user->toString().'"',
			'mainTitle' => $this->get('translator')->trans('admin.user.delete'),
			'backButtonUrl' => $this->generateUrl('admin_user')
		));
    }

    /**
     * Retrieve an existing User entity.
     */
    protected function getUserById($id)
    {
        $user = $this->getDoctrine()->getManager()->getRepository('VMBUserBundle:User')->find($id);

		if ($user == null) {
			throw $this->createNotFoundException('Unable to find User entity.');
		}

		return $user;
    }
}
