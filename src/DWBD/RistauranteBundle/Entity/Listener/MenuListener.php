<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PostUpdate;
use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\SecurityBundle\Entity\RoleEnum;
use DWBD\SecurityBundle\Entity\User;

class MenuListener
{
	/** @var \Swift_Mailer */
	private $mailer;

	/** @var \Twig_Environment */
	private $twig;

	/** @var  string */
	private $mailerUser;

	public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer, $mailerUser)
	{
		$this->mailer = $mailer;
		$this->twig = $twig;
		$this->mailerUser = $mailerUser;
	}

	/**
	 * @PostUpdate()
	 *
	 * @param Menu $menu
	 */
	public function preventAuthorHandler(Menu $menu)
	{
		if ($menu->getPreviousState() != $menu->getState() && $menu->hasBeenRefusedOrValidated()) {
			if ($menu->getAuthor()->getRoles()[0] == RoleEnum::EDITOR) {
				$author = $menu->getAuthor();
				$message = $this->generateMail($menu, "Ristaurante - Your menu had a change of state", $author->getUsername(), $author->getEmail());

				try {
					$this->mailer->send($message);
				} catch (\Exception $e) {
				}
			}
		}
	}

	/**
	 * @PostPersist()
	 * @PostUpdate()
	 *
	 * @param Menu $menu
	 * @param LifecycleEventArgs $event
	 */
	public function preventWaiterHandler(Menu $menu, LifecycleEventArgs $event)
	{
		if (($menu->getPreviousState() != $menu->getState()) || is_null($menu->getPreviousState())
			&& $menu->hasBeenRefusedOrValidated() && $menu->getState() == StateEnum::STATE_VALIDATED
		) {
			$waiters = $event->getObjectManager()->getRepository('DWBDSecurityBundle:User')->findByRoles(array('ROLE_WAITER'));
			$chunk = array_chunk($waiters, 10);

			foreach ($chunk as $emails) {
				$emails = array_map(function (User $user) {
					return $user->getEmail();
				}, $emails);
				$message = $this->generateMail($menu, "[INFO] - Ristaurante - A new menu is available", 'waiter', $emails);

				try {
					$this->mailer->send($message);
				} catch (\Exception $e) {
				}
			}
		}
	}

	/**
	 * @param Menu $menu
	 * @param string $subject
	 * @param string $user
	 * @param string|array $recipient
	 */
	private function generateMail(Menu $menu, $subject, $user, $recipient)
	{
		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($this->mailerUser)
			->setBody(
				$this->twig->render(
					'emails/entity-state-changed.html.twig',
					array(
						'entityName' => 'menu',
						'user' => $user,
						'refused' => $menu->getState() == StateEnum::STATE_REFUSED,
						'title' => $menu->getTitle()
					)
				),
				'text/html'
			)
			->setTo($recipient);
		return $message;
	}
}