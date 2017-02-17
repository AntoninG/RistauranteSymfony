<?php

namespace DWBD\RistauranteBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DWBD\RistauranteBundle\Entity\Enum\CategoryEnum;
use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use DWBD\RistauranteBundle\Form\Type\MenuType;
use DWBD\SecurityBundle\Entity\Enum\RoleEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menu controller.
 *
 * @Route("menus")
 */
class MenuController extends Controller
{
	/**
	 * Lists all menu entities.
	 *
	 * @Route("/index", name="menus_index")
	 * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = $request->get('limit', 15);

		$repository = $this->getDoctrine()->getManager()->getRepository('DWBDRistauranteBundle:Menu');
		$user = $this->get("security.token_storage")->getToken()->getUser();

		if ($user->getRoles()[0] == RoleEnum::WAITER) {
			$menus = $repository->findBy(array('state' => StateEnum::STATE_VALIDATED), array('displayOrder' => 'DESC', 'title' => 'ASC'));
		} else if ($user->getRoles()[0] == RoleEnum::EDITOR) {
			$menus = $repository->findBy(array('author' => $user), array('displayOrder' => 'DESC', 'title' => 'ASC'));
		} else {
			$menus = $repository->findBy(array(), array('displayOrder' => 'DESC', 'title' => 'ASC'));
		}

		$pager = $this->get('app.pager_factory')->createPager($menus, $page, $limit, 'menus_index');

		return $this->render('DWBDRistauranteBundle:menu:index.html.twig', array(
			'title' => 'Menus',
			'active_link' => 'menus',
			'categories' => CategoryEnum::getCategoriesTranslation(),
			'states' => StateEnum::getStatesTranslation(),
			'pager' => $pager
		));
	}

	/**
	 * Creates a new menu entity.
	 *
	 * @Route("/new", name="menus_new")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 *
	 * @param Request $request
	 *
	 * @return Response|RedirectResponse
	 */
	public function newAction(Request $request)
	{
		$user = $this->get("security.token_storage")->getToken()->getUser();
		$isEditor = $user->getRoles()[0] == RoleEnum::EDITOR;

		$menu = new Menu();
		$form = $this->createForm(MenuType::class, $menu, array('isEditor' => $isEditor));
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$menu->setAuthor($user);
			$em->persist($menu);

			try {
				$em->flush();
				return $this->redirectToRoute('menus_show', array('id' => $menu->getId()));
			} catch (UniqueConstraintViolationException $violationException) {
				$this->addFlash('danger', 'The title you choose is already used.');
			} catch (\Exception $e) {
				$this->addFlash('danger', 'An error occurred during creation. Please contact your administrator');
				$this->get('logger')->err($e->getMessage());
			}
		}

		return $this->render('DWBDRistauranteBundle:menu:new.html.twig', array(
			'menu' => $menu,
			'form' => $form->createView(),
			'title' => 'New menu',
			'active_link' => 'menus'
		));
	}

	/**
	 * Finds and displays a menu entity.
	 *
	 * @Route("/show/{id}", name="menus_show", requirements={"id": "\d+"})
	 * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
	 *
	 * @param Menu $menu
	 *
	 * @return Response
	 */
	public function showAction(Menu $menu)
	{
		$deleteForm = $this->createDeleteForm($menu);

		return $this->render('DWBDRistauranteBundle:menu:show.html.twig', array(
			'menu' => $menu,
			'delete_form' => $deleteForm->createView(),
			'title' => $menu->getTitle(),
			'active_link' => 'menus',
			'states' => StateEnum::getStatesTranslation(),
			'categories' => CategoryEnum::getCategoriesTranslation()
		));
	}

	/**
	 * Displays a form to edit an existing menu entity.
	 *
	 * @Route("/edit/{id}", name="menus_edit", requirements={"id": "\d+"})
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 *
	 * @param Request $request
	 * @param Menu $menu
	 *
	 * @return Response|RedirectResponse
	 */
	public function editAction(Request $request, Menu $menu)
	{
		$user = $this->get("security.token_storage")->getToken()->getUser();
		$isEditor = $user->getRoles()[0] == RoleEnum::EDITOR;

		$deleteForm = $this->createDeleteForm($menu);
		$editForm = $this->createForm(MenuType::class, $menu, array(
			'isEditor' => $isEditor,
			'refusedOrValidated' => $menu->hasBeenRefusedOrValidated()
		));
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			try {
				$this->getDoctrine()->getManager()->flush();
				return $this->redirectToRoute('menus_show', array('id' => $menu->getId()));
			} catch (UniqueConstraintViolationException $violationException) {
				$this->addFlash('danger', 'The title you choose is already used.');
			} catch (\Exception $e) {
				$this->addFlash('danger', 'An error occurred during creation. Please contact your administrator');
				$this->get('logger')->err($e->getMessage());
			}
		}

		return $this->render('DWBDRistauranteBundle:menu:edit.html.twig', array(
			'menu' => $menu,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
			'title' => 'Edit ' . $menu->getTitle(),
			'active_link' => 'menus'
		));
	}

	/**
	 * Deletes a menu entity.
	 *
	 * @Route("/delete/{id}", name="menus_delete", requirements={"id": "\d+"})
	 * @Method("DELETE")
	 * @Security("has_role('ROLE_CHIEF')")
	 *
	 * @param Request $request
	 * @param Menu $menu
	 *
	 * @return RedirectResponse
	 */
	public function deleteAction(Request $request, Menu $menu)
	{
		$form = $this->createDeleteForm($menu);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($menu);
			$em->flush();
		}

		return $this->redirectToRoute('menus_index');
	}

	/**
	 * Creates a form to delete a menu entity.
	 *
	 * @param Menu $menu The menu entity
	 *
	 * @return Form The form
	 */
	private function createDeleteForm(Menu $menu)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('menus_delete', array('id' => $menu->getId())))
			->setMethod('DELETE')
			->getForm();
	}
}

