<?php

namespace DWBD\RistauranteBundle\Controller;

use DWBD\RistauranteBundle\Entity\CategoryEnum;
use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\RistauranteBundle\Form\MenuType;
use DWBD\SecurityBundle\Entity\RoleEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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
	 */
	public function indexAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = $request->get('limit', 15);

		$repository = $this->getDoctrine()->getManager()->getRepository('DWBDRistauranteBundle:Menu');
		$user = $this->get("security.token_storage")->getToken()->getUser();

		if ($user->getRole()[0] == RoleEnum::WAITER) {
			$totalRows = count($repository->findBy(array('state' => StateEnum::STATE_VALIDATED)));
		} else if ($user->getRole()[0] == RoleEnum::EDITOR) {
			$totalRows = count($repository->findBy(array('author' => $user)));
		} else {
			$totalRows = count($repository->findAll());
		}

		$page = $page < 1 ? 1 : $page;
		$start = ($page - 1) * $limit;
		$last = ceil($totalRows / $limit);
		$last = $last == 0 ? 1 : $last;
		$lastMinusOne = $last - 1;

		if ($user->getRole()[0] == RoleEnum::WAITER) {
			$menus = $repository->findBy(array('state' => StateEnum::STATE_VALIDATED), array('displayOrder' => 'DESC', 'title' => 'ASC'), $limit, $start);
		} else if ($user->getRole()[0] == RoleEnum::EDITOR) {
			$menus = $repository->findBy(array('author' => $user), array('displayOrder' => 'DESC', 'title' => 'ASC'), $limit, $start);
		} else {
			$menus = $repository->findBy(array(), array('displayOrder' => 'DESC', 'title' => 'ASC'), $limit, $start);
		}

		return $this->render('DWBDRistauranteBundle:menu:index.html.twig', array(
			'menus' => $menus,
			'title' => 'Menus',
			'active_link' => 'menus',
			'categories' => CategoryEnum::getCategoriesTranslation(),
			'states' => StateEnum::getStatesTranslation(),
			'number' => ($start + 1) . ' to ' . ($start + count($menus)) . ' / ' . $totalRows . ' entries',
			'page' => $page,
			'last' => $last,
			'lastMinusOne' => $lastMinusOne
		));
	}

	/**
	 * Creates a new menu entity.
	 *
	 * @Route("/new", name="menus_new")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 */
	public function newAction(Request $request)
	{
		$options = array('isCreation' => true);
		$menu = new Menu();
		$form = $this->createForm(MenuType::class, $menu, $options);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->get("security.token_storage")->getToken()->getUser();
			$menu->setAuthor($user)
				->setState(StateEnum::STATE_DRAFT);
			$em->persist($menu);
			$em->flush($menu);

			return $this->redirectToRoute('menus_show', array('id' => $menu->getId()));
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
	 * @Route("/show/{id}", name="menus_show")
	 * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
	 */
	public function showAction(Menu $menu)
	{
		$deleteForm = $this->createDeleteForm($menu);

		return $this->render('DWBDRistauranteBundle:menu:show.html.twig', array(
			'menu' => $menu,
			'delete_form' => $deleteForm->createView(),
			'title' => $menu->getTitle(),
			'active_link' => 'menus',
			'states' => StateEnum::getStatesTranslation()
		));
	}

	/**
	 * Displays a form to edit an existing menu entity.
	 *
	 * @Route("/edit/{id}", name="menus_edit")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 */
	public function editAction(Request $request, Menu $menu)
	{
		$deleteForm = $this->createDeleteForm($menu);
		$editForm = $this->createForm(MenuType::class, $menu);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('menus_edit', array('id' => $menu->getId()));
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
	 * @Route("/delete/{id}", name="menus_delete")
	 * @Method("DELETE")
	 * @Security("has_role('ROLE_CHIEF')")
	 */
	public function deleteAction(Request $request, Menu $menu)
	{
		$form = $this->createDeleteForm($menu);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($menu);
			$em->flush($menu);
		}

		return $this->redirectToRoute('menus_index');
	}

	/**
	 * Creates a form to delete a menu entity.
	 *
	 * @param Menu $menu The menu entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Menu $menu)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('menus_delete', array('id' => $menu->getId())))
			->setMethod('DELETE')
			->getForm();
	}
}
