<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PostUpdate;
use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use DWBD\SecurityBundle\Entity\Enum\RoleEnum;
use DWBD\SecurityBundle\Entity\User;

/**
 * Class MenuMailListener
 * This is an EntityListener for Menu entity
 * It manages all mails related to the menu
 *
 * @package DWBD\RistauranteBundle\Entity\Listener
 */
class MenuMailListener
{
	/** @var \Swift_Mailer */
	private $mailer;

	/** @var \Twig_Environment */
	private $twig;

	/** @var  string */
	private $mailerUser;

	/**
	 * MenuMailListener constructor.
	 *
	 * @param \Twig_Environment $twig
	 * @param \Swift_Mailer $mailer
	 * @param $mailerUser
	 */
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
				$message = $this->generateMail(
					'entity-state-changed.html.twig',
					$menu,
					"Ristaurante - Your menu had a change of state",
					$author->getUsername(),
					$author->getEmail()
				);

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
				$message = $this->generateMail(
					'entity-state-changed.html.twig',
					$menu,
					"[INFO] - Ristaurante - A new menu is available",
					'waiter',
					$emails
				);

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
	public function preventPublishersHandler(Menu $menu, LifecycleEventArgs $event)
	{
		if (($menu->getPreviousState() != $menu->getState()) || is_null($menu->getPreviousState()) && $menu->getState() == StateEnum::STATE_WAITING) {
			if ($menu->getAuthor()->getRoles()[0] == RoleEnum::EDITOR) {
				$publishers = $event->getObjectManager()->getRepository('DWBDSecurityBundle:User')->findByRoles(array(
					'ROLE_REVIEWER', 'ROLE_CHIEF', 'ROLE_ADMIN'
				));
				$chunk = array_chunk($publishers, 10);

				foreach ($chunk as $emails) {
					$emails = array_map(function (User $user) {
						return $user->getEmail();
					}, $emails);
					$message = $this->generateMail(
						'entity-waiting-state.html.twig',
						$menu,
						"[INFO] - Ristaurante - A new menu is waiting for validation",
						'publisher',
						$emails
					);

					try {
						$this->mailer->send($message);
					} catch (\Exception $e) {
					}
				}
			}
		}
	}

	/**
	 * Generate a mail base on parameters
	 *
	 * @param string $view
	 * @param Menu $menu
	 * @param string $subject
	 * @param string $user
	 * @param string|array $recipient
	 *
	 * @return \Swift_Message
	 */
	private function generateMail($view, Menu $menu, $subject, $user, $recipient)
	{
		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($this->mailerUser)
			->setBody(
				$this->twig->render(
					'emails/' . $view,
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
