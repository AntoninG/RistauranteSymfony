<?php

namespace DWBD\SecurityBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DWBD\SecurityBundle\Entity\User;
use DWBD\SecurityBundle\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

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
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = $request->get('limit', 15);

		$repository = $this->getDoctrine()->getManager()->getRepository('DWBDSecurityBundle:User');
		$users = $repository->findAll();

		$pager = $this->get('app.pager_factory')->createPager($users, $page, $limit, 'user_index');

		return $this->render('DWBDSecurityBundle:user:index.html.twig', array(
			'title' => 'Users',
			'pager' => $pager,
			'active_link' => 'users'
		));
	}

	/**
	 * Creates a new user entity.
	 *
	 * @Route("/new", name="user_new")
	 * @Method({"GET", "POST"})
	 *
	 * @param Request $request
	 * @return Response|RedirectResponse
	 */
	public function newAction(Request $request)
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$encoder = $this->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($encoded);
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);

			try {
				$em->flush();
				return $this->redirectToRoute('user_show', array('id' => $user->getId()));
			} catch (UniqueConstraintViolationException $exception) {
				$this->addFlash('danger', 'The email or login you tried are already used');
			} catch (\Exception $e) {
				$this->addFlash('danger', 'An error occurred during creation. Please contact your administrator');
				$this->get('logger')->err($e->getMessage());
			}

		}

		return $this->render('DWBDSecurityBundle:user:new.html.twig', array(
			'user' => $user,
			'form' => $form->createView(),
			'title' => 'New user',
			'active_link' => 'users'
		));
	}

	/**
	 * Finds and displays a user entity.
	 *
	 * @Route("/{id}", name="user_show", requirements={"id": "\d+"})
	 * @Method("GET")
	 *
	 * @param User $user
	 * @return Response
	 */
	public function showAction(User $user)
	{
		$deleteForm = $this->createDeleteForm($user);

		return $this->render('DWBDSecurityBundle:user:show.html.twig', array(
			'user' => $user,
			'delete_form' => $deleteForm->createView(),
			'title' => $user->getUsername(),
			'active_link' => 'users'
		));
	}

	/**
	 * Displays a form to edit an existing user entity.
	 *
	 * @Route("/edit/{id}", name="user_edit", requirements={"id": "\d+"})
	 * @Method({"GET", "POST"})
	 *
	 * @param Request $request
	 * @param User $user
	 *
	 * @return Response|RedirectResponse
	 */
	public function editAction(Request $request, User $user)
	{
		$deleteForm = $this->createDeleteForm($user);
		$editForm = $this->createForm(UserType::class, $user);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$encoder = $this->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($encoded);

			try {
				$this->getDoctrine()->getManager()->flush();
				return $this->redirectToRoute('user_show', array('id' => $user->getId()));
			} catch (UniqueConstraintViolationException $exception) {
				$this->addFlash('danger', 'The email or login you tried are already used');
			} catch (\Exception $e) {
				$this->addFlash('danger', 'An error occurred during edition. Please contact your administrator');
				$this->get('logger')->err($e->getMessage());
			}
		}

		return $this->render('DWBDSecurityBundle:user:edit.html.twig', array(
			'user' => $user,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
			'title' => 'Edit ' . $user->getUsername(),
			'active_link' => 'users'
		));
	}

	/**
	 * Deletes a user entity.
	 *
	 * @Route("/delete/{id}", name="user_delete", requirements={"id": "\d+"})
	 * @Method({"DELETE", "GET"})
	 *
	 * @param Request $request
	 * @param User $user
	 *
	 * @return RedirectResponse
	 */
	public function deleteAction(Request $request, User $user)
	{
		$form = $this->createDeleteForm($user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($user);
			$em->flush();
		}

		return $this->redirectToRoute('user_index');
	}

	/**
	 * Creates a form to delete a user entity.
	 *
	 * @param User $user The user entity
	 *
	 * @return Form The form
	 */
	private function createDeleteForm(User $user)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
			->setMethod('DELETE')
			->getForm();
	}
}
