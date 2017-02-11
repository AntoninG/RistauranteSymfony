<?php

namespace DWBD\SecurityBundle\Controller;

use DWBD\SecurityBundle\Entity\User;
use DWBD\SecurityBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Route("admin/users")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller
{
	/**
     * Lists all user entities.
     *
     * @Route("/index", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('DWBDSecurityBundle:User')->findAll();

        return $this->render('DWBDSecurityBundle:user:index.html.twig', array(
            'users' => $users,
			'title' => 'Users'
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
        	if ($form->isValid()) {
				$encoder = $this->get('security.password_encoder');
				$encoded = $encoder->encodePassword($user, $user->getPassword());
				$user->setPassword($encoded);
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush($user);

				return $this->redirectToRoute('user_show', array('id' => $user->getId()));
			} else {
        		$this->addFlash('danger', 'There are errors in the form');
			}
        }

        return $this->render('DWBDSecurityBundle:user:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
			'title' => 'New user'
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('DWBDSecurityBundle:user:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
			'title' => $user->getUsername()
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/edit/{id}", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()){
        	if ($editForm->isValid()) {
				$encoder = $this->get('security.password_encoder');
				$encoded = $encoder->encodePassword($user, $user->getPassword());
				$user->setPassword($encoded);
				$this->getDoctrine()->getManager()->flush();

				return $this->redirectToRoute('user_show', array('id' => $user->getId()));
			} else {
				$this->addFlash('danger', 'There are errors in the form');
			}
        }

        return $this->render('DWBDSecurityBundle:user:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
			'title' => 'Edit '.$user->getUsername()
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("/delete/{id}", name="user_delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush($user);
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
