<?php

namespace DWBD\RistauranteBundle\Controller;

use DWBD\RistauranteBundle\Entity\Reservation;
use DWBD\RistauranteBundle\Form\ReservationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Reservation controller.
 *
 * @Route("reservations")
 */
class ReservationController extends Controller
{
	/**
	 * Lists all reservation entities.
	 *
	 * @Route("/index", name="reservations_index")
	 * @Method("GET")
	 *
	 * @Security("has_role('ROLE_CHIEF')")
	 */
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();

		$reservations = $em->getRepository('DWBDRistauranteBundle:Reservation')->findAll();

		return $this->render('DWBDRistauranteBundle:reservation:index.html.twig', array(
			'reservations' => $reservations,
			'active_link' => 'reservations'
		));
	}

	/**
	 * Creates a new reservation entity.
	 *
	 * @Route("/new", name="reservations_new")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_USER')")
	 */
	public function newAction(Request $request)
	{
		$reservation = new Reservation();
		$form = $this->createForm(ReservationType::class, $reservation);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($reservation);
			$em->flush($reservation);

			return $this->redirectToRoute('reservations_show', array('id' => $reservation->getId()));
		}

		return $this->render('DWBDRistauranteBundle:reservation:new.html.twig', array(
			'reservation' => $reservation,
			'form' => $form->createView(),
			'active_link' => 'reservations'
		));
	}

	/**
	 * Finds and displays a reservation entity.
	 *
	 * @Route("/show/{id}", name="reservations_show")
	 * @Method("GET")
	 * @Security("has_role('ROLE_CHIEF')")
	 */
	public function showAction(Reservation $reservation)
	{
		$deleteForm = $this->createDeleteForm($reservation);

		return $this->render('DWBDRistauranteBundle:reservation:show.html.twig', array(
			'reservation' => $reservation,
			'delete_form' => $deleteForm->createView(),
			'active_link' => 'reservations'
		));
	}

	/**
	 * Displays a form to edit an existing reservation entity.
	 *
	 * @Route("/edit/{id}", name="reservations_edit")
	 * @Method({"GET", "POST"})
	 * @Security("has_role('ROLE_CHIEF')")
	 */
	public function editAction(Request $request, Reservation $reservation)
	{
		$deleteForm = $this->createDeleteForm($reservation);
		$editForm = $this->createForm(ReservationType::class, $reservation);
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute('reservations_edit', array('id' => $reservation->getId()));
		}

		return $this->render('DWBDRistauranteBundle:reservation:edit.html.twig', array(
			'reservation' => $reservation,
			'edit_form' => $editForm->createView(),
			'delete_form' => $deleteForm->createView(),
			'active_link' => 'reservations'
		));
	}

	/**
	 * Deletes a reservation entity.
	 *
	 * @Route("/delete/{id}", name="reservations_delete")
	 * @Method("DELETE")
	 * @Security("has_role('ROLE_CHIEF')")
	 */
	public function deleteAction(Request $request, Reservation $reservation)
	{
		$form = $this->createDeleteForm($reservation);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($reservation);
			$em->flush($reservation);
		}

		return $this->redirectToRoute('reservations_index');
	}

	/**
	 * Creates a form to delete a reservation entity.
	 *
	 * @param Reservation $reservation The reservation entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm(Reservation $reservation)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('reservations_delete', array('id' => $reservation->getId())))
			->setMethod('DELETE')
			->getForm();
	}
}
