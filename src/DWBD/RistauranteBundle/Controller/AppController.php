<?php

namespace DWBD\RistauranteBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AppController extends Controller
{
	/**
	 * @Route("/")
	 * @Route("/home", name="home")
	 */
	public function homeAction()
	{
		return $this->render('base.html.twig', array(
			'title' => 'Ristaurante App',
			'active_link' => 'home'
		));
	}
}
