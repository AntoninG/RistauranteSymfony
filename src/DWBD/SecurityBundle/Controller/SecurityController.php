<?php

namespace DWBD\SecurityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

/**
 * Class SecurityController
 * @package DWBD\SecurityBundle\Controller
 *
 */
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
		if (!is_null($error)) {
			$this->addFlash('danger', 'An error occurred.');
		}

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

	/**
	 * @Route("/reset", name="reset_password")
	 * @Method({"GET", "POST"})
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function resetPasswordAction(Request $request)
	{

	}
}