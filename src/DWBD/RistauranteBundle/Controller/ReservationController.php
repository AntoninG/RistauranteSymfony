<?php

namespace DWBD\RistauranteBundle\Controller;

use DWBD\RistauranteBundle\Entity\Reservation;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\RistauranteBundle\Form\Type\ReservationType;
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
	public function indexAction(Request $request)
	{
		$page = $request->get('page', 1);
		$limit = $request->get('limit', 15);

		$em = $this->getDoctrine()->getManager();
		$reservations = $em->getRepository('DWBDRistauranteBundle:Reservation')->findBy(array('state' => StateEnum::STATE_WAITING), array('date' => 'ASC', 'time' => 'ASC'));

		$pager = $this->get('app.pager_factory')->createPager($reservations, $page, $limit, 'reservations_index');

		return $this->render('DWBDRistauranteBundle:reservation:index.html.twig', array(
			'title' => 'Reservations',
			'pager' => $pager,
			'states' => StateEnum::getStatesTranslation(),
			'active_link' => 'reservations'
		));
	}

	/**
	 * Creates a new reservation entity.
	 *
	 * @Route("/new", name="reservations_new")
	 * @Method({"GET", "POST"})
	 */
	public function newAction(Request $request)
	{
		$securityContext = $this->get('security.authorization_checker');
		if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
			throw $this->createAccessDeniedException();
		}

		$reservation = new Reservation();
		$form = $this->createForm(ReservationType::class, $reservation);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if ($reservation->getDate()->getTimestamp() > mktime(23, 59, 59)) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($reservation);
				try {
					$em->flush($reservation);
					$this->addFlash('success', 'Your reservation has been saved');
					return $this->redirectToRoute('home');
				} catch (\Exception $e) {
					$this->addFlash('danger', 'An error occurred, please, contact an administrator');
				}
			} else {
				$this->addFlash('danger', 'Your reservation must be for tomorrow or later');
			}
		}

		return $this->render('DWBDRistauranteBundle:reservation:new.html.twig', array(
			'reservation' => $reservation,
			'form' => $form->createView(),
			'title' => 'New reservation',
			'active_link' => 'reservations'
		));
	}

	/**
	 * @Route("/validate/{id}", name="reservations_validate")
	 * @Method({"GET"})
	 * @Security("has_role('ROLE_CHIEF')")
	 *
	 * @param Request $request
	 * @param Reservation $reservation
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function validateAjaxAction(Request $request, Reservation $reservation)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->json(array());
		}

		$reservation->setState(StateEnum::STATE_VALIDATED);
		try {
			$this->getDoctrine()->getManager()->flush();
			return $this->json(array('error' => false));
		} catch (\Exception $e) {
			return $this->json(array('error' => $e->getMessage(), 500));
		}
	}

	/**
	 * @Route("/refuse/{id}", name="reservations_refuse")
	 * @Method({"GET"})
	 * @Security("has_role('ROLE_CHIEF')")
	 *
	 * @param Request $request
	 * @param Reservation $reservation
	 *
	 * @return \Symfony\Component\HttpFoundation\JsonResponse
	 */
	public function refuseAjaxAction(Request $request, Reservation $reservation)
	{
		if (!$request->isXmlHttpRequest()) {
			return $this->json(array());
		}

		$reservation->setState(StateEnum::STATE_REFUSED);
		try {
			$this->getDoctrine()->getManager()->flush();
			return $this->json(array('error' => false));
		} catch (\Exception $e) {
			return $this->json(array('error' => $e->getMessage(), 500));
		}
	}
}

