<?php

namespace DWBD\RistauranteBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DWBD\RistauranteBundle\Entity\Enum\CategoryEnum;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use DWBD\RistauranteBundle\Form\Type\DishType;
use DWBD\SecurityBundle\Entity\Enum\RoleEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

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
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function indexAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = $request->get('limit', 15);

		$repository = $this->getDoctrine()->getManager()->getRepository('DWBDRistauranteBundle:Dish');
		$user = $this->get("security.token_storage")->getToken()->getUser();

		if ($user->getRoles()[0] == RoleEnum::WAITER) {
			$dishes = $repository->findBy(array('state' => StateEnum::STATE_VALIDATED), array('title' => 'ASC'));
		} else if ($user->getRoles()[0] == RoleEnum::EDITOR) {
			$dishes = $repository->findBy(array('author' => $user), array('title' => 'ASC'));
		} else {
			$dishes = $repository->findBy(array(), array('title' => 'ASC'));
		}

		$pager = $this->get('app.pager_factory')->createPager($dishes, $page, $limit, 'dishes_index');

		return $this->render('DWBDRistauranteBundle:dish:index.html.twig', array(
			'title' => 'Dishes',
			'categories' => CategoryEnum::getCategoriesTranslation(),
			'states' => StateEnum::getStatesTranslation(),
			'pager' => $pager,
			'active_link' => 'dishes'
		));
	}

	/**
	 * Creates a new dish entity.
	 *
	 * @Route("/new", name="dishes_new")
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

		$dish = new Dish();
		$form = $this->createForm(DishType::class, $dish, array('isEditor' => $isEditor));
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$dish->setAuthor($user);

			$em = $this->getDoctrine()->getManager();
			$em->persist($dish);

			try {
				$em->flush();
				return $this->redirectToRoute('dishes_show', array('id' => $dish->getId()));
			} catch (UniqueConstraintViolationException $violationException) {
				$this->addFlash('danger', 'The title you choose is already used.');
			} catch (\Exception $e) {
				$this->addFlash('danger', 'An error occurred during creation. Please contact your administrator');
				$this->get('logger')->err($e->getMessage());
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
	 * @Route("/show/{id}", name="dishes_show", requirements={"id": "\d+"})
	 * @Method("GET")
	 * @Security("has_role('ROLE_WAITER')")
	 *
	 * @param Dish $dish
	 *
	 * @return Response
	 */
	public function showAction(Dish $dish)
	{
		$role = $this->get('security.token_storage')->getToken()->getUser()->getRoles()[0];
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
	 * @Route("/edit/{id}", name="dishes_edit", requirements={"id": "\d+"})
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_EDITOR')")
	 *
	 * @param Request $request
	 * @param Dish $dish
	 *
	 * @return Response|RedirectResponse
	 */
	public function editAction(Request $request, Dish $dish)
	{
		$user = $this->get("security.token_storage")->getToken()->getUser();
		$isEditor = $user->getRoles()[0] == RoleEnum::EDITOR;

		$deleteForm = $this->createDeleteForm($dish);
		$options = array('isEditor' => $isEditor, 'refusedOrValidated' => $dish->hasBeenRefusedOrValidated());
		$editForm = $this->createForm(DishType::class, $dish, $options);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			try {
				$this->getDoctrine()->getManager()->flush();
				return $this->redirectToRoute('dishes_show', array('id' => $dish->getId()));
			} catch (UniqueConstraintViolationException $exception) {
				$this->addFlash('danger', 'The title you choose is already used.');
			} catch (\Exception $e) {
				$this->addFlash('danger', 'An error occurred during creation. Please contact your administrator');
				$this->get('logger')->err($e->getMessage());
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
	 * @Route("/delete/{id}", name="dishes_delete", requirements={"id": "\d+"})
	 * @Method("DELETE")
	 * @Security("has_role('ROLE_CHIEF')")
	 *
	 * @param Request $request
	 * @param Dish $dish
	 *
	 * @return RedirectResponse
	 */
	public function deleteAction(Request $request, Dish $dish)
	{
		$form = $this->createDeleteForm($dish);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($dish);
			$em->flush();
		}

		return $this->redirectToRoute('dishes_index');
	}

	/**
	 * Creates a form to delete a dish entity.
	 *
	 * @param Dish $dish The dish entity
	 *
	 * @return Form The form
	 */
	private function createDeleteForm(Dish $dish)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('dishes_delete', array('id' => $dish->getId())))
			->setMethod('DELETE')
			->getForm();
	}


}

