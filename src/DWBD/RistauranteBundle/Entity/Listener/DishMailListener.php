<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PostUpdate;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use DWBD\SecurityBundle\Entity\Enum\RoleEnum;
use DWBD\SecurityBundle\Entity\User;

/**
 * Class DishMailListener
 * This is an EntityListener for Dish entity
 * It manages all mails related to the dish
 *
 * @package DWBD\RistauranteBundle\Entity\Listener
 */
class DishMailListener
{
	/** @var \Swift_Mailer */
	private $mailer;

	/** @var \Twig_Environment */
	private $twig;

	/** @var  string */
	private $mailerUser;

	/**
	 * DishMailListener constructor.
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
	 * @param Dish $dish
	 */
	public function preventAuthorHandler(Dish $dish)
	{
		if ($dish->getPreviousState() != $dish->getState() && $dish->hasBeenRefusedOrValidated()) {
			if ($dish->getAuthor()->getRoles()[0] == RoleEnum::EDITOR) {
				$author = $dish->getAuthor();
				$message = $this->generateMail(
					'entity-state-changed.html.twig',
					$dish,
					"Ristaurante - Your dish had a change of state",
					$author->getUsername(),
					$author->getEmail());

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
	 * @param Dish $dish
	 * @param LifecycleEventArgs $event
	 */
	public function preventWaiterHandler(Dish $dish, LifecycleEventArgs $event)
	{
		if (($dish->getPreviousState() != $dish->getState()) || is_null($dish->getPreviousState())
			&& $dish->hasBeenRefusedOrValidated() && $dish->getState() == StateEnum::STATE_VALIDATED
		) {
			$waiters = $event->getObjectManager()->getRepository('DWBDSecurityBundle:User')->findByRoles(array('ROLE_WAITER'));
			$chunk = array_chunk($waiters, 10);

			foreach ($chunk as $emails) {
				$emails = array_map(function (User $user) {
					return $user->getEmail();
				}, $emails);
				$message = $this->generateMail(
					'entity-state-changed.html.twig',
					$dish,
					"[INFO] - Ristaurante - A new dish is available",
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
	 * @param Dish $dish
	 * @param LifecycleEventArgs $event
	 */
	public function preventPublishersHandler(Dish $dish, LifecycleEventArgs $event)
	{
		if (($dish->getPreviousState() != $dish->getState()) || is_null($dish->getPreviousState()) && $dish->getState() == StateEnum::STATE_WAITING) {
			if ($dish->getAuthor()->getRoles()[0] == RoleEnum::EDITOR) {
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
						$dish,
						"[INFO] - Ristaurante - A new dish is waiting for validation", '
						publisher',
						$emails);

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
	 * @param Dish $dish
	 * @param string $subject
	 * @param string $user
	 * @param string|array $recipient
	 *
	 * @return \Swift_Message
	 */
	private function generateMail($view, Dish $dish, $subject, $user, $recipient)
	{
		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($this->mailerUser)
			->setBody(
				$this->twig->render(
					'emails/' . $view,
					array(
						'entityName' => 'dish',
						'user' => $user,
						'refused' => $dish->getState() == StateEnum::STATE_REFUSED,
						'title' => $dish->getTitle()
					)
				),
				'text/html'
			)
			->setTo($recipient);
		return $message;
	}
}
