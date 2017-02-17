<?php

namespace DWBD\RistauranteBundle\Controller;


use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AppController extends Controller
{
	/**
	 * Display the home according to the user asking
	 *
	 * @Route("/")
	 * @Route("/home", name="home")
	 *
	 * @return Response
	 */
	public function homeAction()
	{
		$title = 'Ristaurante Bellissima';

		if ($this->isGranted('ROLE_REVIEWER')) {
			return $this->getPublishersHome($title);
		} elseif ($this->isGranted('ROLE_EDITOR')) {
			return $this->getEditorHome($title);
		} elseif ($this->isGranted('ROLE_WAITER')) {
			return $this->getWaiterHome($title);
		} else {
			return $this->getAnonymousHome($title);
		}
	}

	/**
	 * Returns the home for anonymous users
	 *
	 * @param string $title
	 * @return Response
	 */
	private function getAnonymousHome($title)
	{
		return $this->render('DWBDRistauranteBundle:app:anonymous-home.html.twig', array('title' => $title));
	}

	/**
	 * Returns the home for waiters
	 *
	 * @param string $title
	 * @return Response
	 */
	private function getWaiterHome($title)
	{
		$menus = $this->getDoctrine()->getManager()->getRepository('DWBDRistauranteBundle:Menu')->findBy(
			array('state' => StateEnum::STATE_VALIDATED),
			array('displayOrder' => 'DESC', 'title' => 'ASC'),
			5
		);
		return $this->render('DWBDRistauranteBundle:app:waiter-home.html.twig', array(
			'title' => $title, 'menus' => $menus
		));
	}

	/**
	 * Returns the home for editors and reviewers
	 *
	 * @param string $title
	 * @return Response
	 */
	private function getEditorHome($title)
	{
		return $this->render('DWBDRistauranteBundle:app:editor-home.html.twig', array('title' => $title));
	}

	/**
	 * Returns the home for chiefs and administrators
	 *
	 * @param string $title
	 * @return Response
	 */
	private function getPublishersHome($title)
	{
		$em = $this->getDoctrine()->getManager();

		$reservations = array();
		if ($this->isGranted('ROLE_CHIEF')) {
			$reservations = $em->getRepository('DWBDRistauranteBundle:Reservation')->findBy(
				array('state' => StateEnum::STATE_WAITING),
				array('date' => 'ASC', 'time' => 'ASC', 'number' => 'DESC', 'name' => 'ASC'),
				5
			);
		}

		$menus = $em->getRepository('DWBDRistauranteBundle:Menu')->findBy(
			array('state' => StateEnum::STATE_WAITING),
			array('displayOrder' => 'DESC', 'title' => 'ASC'),
			5
		);

		$dishes = $em->getRepository('DWBDRistauranteBundle:Dish')->findBy(
			array('state' => StateEnum::STATE_WAITING),
			array('title' => 'ASC'),
			5
		);

		return $this->render('DWBDRistauranteBundle:app:publishers-home.html.twig', array(
			'title' => $title,
			'reservations' => $reservations,
			'menus' => $menus,
			'dishes' => $dishes
		));
	}
}
