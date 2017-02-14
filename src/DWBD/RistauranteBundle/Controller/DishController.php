<?php

namespace DWBD\RistauranteBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DWBD\RistauranteBundle\Entity\CategoryEnum;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\RistauranteBundle\Form\DishType;
use DWBD\SecurityBundle\Entity\RoleEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Dish controller.
 *
 * @Route("dishes")
 */
class DishController extends Controller
{
	/**
	 * Lists all dish entities.
	 *
	 * @Route("/index", name="dishes_index")
	 * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
	 */
	public function indexAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = $request->get('limit', 15);

		$repository = $this->getDoctrine()->getManager()->getRepository('DWBDRistauranteBundle:Dish');
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
			$dishes = $repository->findBy(array('state' => StateEnum::STATE_VALIDATED), array('title' => 'ASC'), $limit, $start);
		} else if ($user->getRole()[0] == RoleEnum::EDITOR) {
			$dishes = $repository->findBy(array('author' => $user), array('title' => 'ASC'), $limit, $start);
		} else {
			$dishes = $repository->findBy(array(), array('title' => 'ASC'), $limit, $start);
		}

		return $this->render('DWBDRistauranteBundle:dish:index.html.twig', array(
			'dishes' => $dishes,
			'title' => 'Dishes',
			'categories' => CategoryEnum::getCategoriesTranslation(),
			'states' => StateEnum::getStatesTranslation(),
			'number' => ($start + 1) . ' to ' . ($start + count($dishes)) . ' / ' . $totalRows . ' entries',
			'page' => $page,
			'last' => $last,
			'lastMinusOne' => $lastMinusOne,
			'active_link' => 'dishes'
		));
	}

	/**
	 * Creates a new dish entity.
	 *
	 * @Route("/new", name="dishes_new")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 */
	public function newAction(Request $request)
	{
		$user = $this->get("security.token_storage")->getToken()->getUser();
		$isEditor = $user->getRole()[0] == RoleEnum::EDITOR;
		$options = array('isEditor' => $isEditor);

		$dish = new Dish();
		$form = $this->createForm(DishType::class, $dish, $options);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();

				$dish->setAuthor($user);
				$check = $dish->checkHasBeenRefusedOrValidated();
				$em->persist($dish);

				try {
					$em->flush($dish);
					return $this->redirectToRoute('dishes_show', array('id' => $dish->getId()));
				} catch (UniqueConstraintViolationException $exception) {
					$this->addFlash('danger', 'The title you choose is already used.');
				}
			} else {
				$this->addFlash('danger', 'There are errors in the form');
			}
		}

		return $this->render('DWBDRistauranteBundle:dish:new.html.twig', array(
			'dish' => $dish,
			'form' => $form->createView(),
			'title' => 'New dish',
			'active_link' => 'dishes'
		));
	}

	/**
	 * Finds and displays a dish entity.
	 *
	 * @Route("/show/{id}", name="dishes_show")
	 * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
	 */
	public function showAction(Dish $dish)
	{
		$role = $this->get('security.token_storage')->getToken()->getUser()->getRole()[0];
		if ($role == RoleEnum::WAITER && $dish->getState() != StateEnum::STATE_VALIDATED) {
			throw $this->createAccessDeniedException();
		}

		$deleteForm = $this->createDeleteForm($dish);

		return $this->render('DWBDRistauranteBundle:dish:show.html.twig', array(
			'dish' => $dish,
			'delete_form' => $deleteForm->createView(),
			'title' => $dish->getTitle(),
			'active_link' => 'dishes',
			'categories' => CategoryEnum::getCategoriesTranslation(),
			'states' => StateEnum::getStatesTranslation()
		));
	}

	/**
	 * Displays a form to edit an existing dish entity.
	 *
	 * @Route("/edit/{id}", name="dishes_edit")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 */
	public function editAction(Request $request, Dish $dish)
	{
		$user = $this->get("security.token_storage")->getToken()->getUser();
		$isEditor = $user->getRole()[0] == RoleEnum::EDITOR;
		$options = array(
			'isEditor' => $isEditor,
			'refusedOrValidated' => $dish->hasBeenRefusedOrValidated()
		);

		$deleteForm = $this->createDeleteForm($dish);
		$editForm = $this->createForm(DishType::class, $dish, $options);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted()) {
			if ($editForm->isValid()) {
				$dish->checkHasBeenRefusedOrValidated();

				try {
					$this->getDoctrine()->getManager()->flush();
					return $this->redirectToRoute('dishes_show', array('id' => $dish->getId()));
				} catch (UniqueConstraintViolationException $exception) {
					$this->addFlash('danger', 'The title you choose is already used.');
				}
			} else {
				$this->addFlash('danger', 'There are errors in the form');
			}
		}

		return $this->render('DWBDRistauranteBundle:dish:edit.html.twig', array(
			'dish' => $dish,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
			'title' => 'Edit ' . $dish->getTitle(),
			'refusedOrValidated' => $options['refusedOrValidated'],
			'active_link' => 'dishes'
		));
	}

	/**
	 * Deletes a dish entity.
	 *
	 * @Route("/delete/{id}", name="dishes_delete")
	 * @Method("DELETE")
	 * @Security("has_role('ROLE_CHIEF')")
	 */
	public function deleteAction(Request $request, Dish $dish)
	{
		$form = $this->createDeleteForm($dish);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($dish);
			$em->flush($dish);
		}

		return $this->redirectToRoute('dishes_index');
	}

	/**
	 * Creates a form to delete a dish entity.
	 *
	 * @param Dish $dish The dish entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Dish $dish)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('dishes_delete', array('id' => $dish->getId())))
			->setMethod('DELETE')
			->getForm();
	}

}
