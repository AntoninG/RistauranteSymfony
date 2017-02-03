<?php

namespace DWBD\SecurityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
	/**
	 * @Route("/login", name="login")
	 * @Method({"GET", "POST"})
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function loginAction(Request $request)
	{
		$authenticationUtils = $this->get('security.authentication_utils');

		$error = $authenticationUtils->getLastAuthenticationError();

		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('DWBDSecurityBundle:security:login.html.twig', array(
			'last_username' => $lastUsername,
			'error'         => $error,
		));
	}

	/**
	 * @Route("/logout", name="logout")
	 *
	 * @param Request $request
	 */
	public function logoutAction(Request $request)
	{

	}
}