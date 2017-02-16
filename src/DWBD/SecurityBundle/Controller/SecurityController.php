<?php

namespace DWBD\SecurityBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
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
			'error' => $error,
			'title' => 'Login'
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
	 * @Method({"POST"})
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function resetPasswordAction(Request $request)
	{
		$email = $request->request->get('resetEmail');
		$email = trim($email);

		$repository = $this->getDoctrine()->getRepository('DWBDSecurityBundle:User');
		$user = $repository->findOneBy(array('email' => $email));

		if (is_null($user) || !is_object($user)) {
			$this->addFlash('danger', 'No user found on for this email. No password reset was performed.');
			return $this->redirectToRoute('login');
		}

		$newPassword = $this->randomPassword();
		$encoded = $this->get('security.password_encoder')->encodePassword($user, $newPassword);
		$user->setPassword($encoded);

		$em = $this->getDoctrine()->getManager();
		$em->persist($user);

		try {
			$em->flush($user);
		} catch (Exception $e) {
			$this->addFlash('danger', 'An error occurred during the process. Please contact your administrator.');
			$this->get('logger')->error($e->getMessage());
			return $this->redirectToRoute('login');
		}

		// I know, not a good idea to send a mail with the plain password
		// But I don't give a f**k to push security to this level on this project =S
		$message = \Swift_Message::newInstance()->setSubject("[INFO] - Ristaurante - Password reset")
			->setFrom($this->getParameter("mailer_user"))->setTo($email)
			->setBody(
				$this->renderView(
					'emails/reset-password.html.twig',
					array('password' => $newPassword)
				),
				'text/html'
			);
		$this->get("mailer")->send($message);

		$this->addFlash('success', 'A mail was sent to ' . $email . ' with your new password');
		return $this->redirectToRoute('login');
	}

	private function randomPassword()
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}
}
