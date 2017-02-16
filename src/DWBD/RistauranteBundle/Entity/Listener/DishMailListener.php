<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PostPersist;
use Doctrine\ORM\Mapping\PostUpdate;
use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\StateEnum;
use DWBD\SecurityBundle\Entity\RoleEnum;
use DWBD\SecurityBundle\Entity\User;

class DishMailListener
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
	 * @param Dish $dish
	 */
	public function preventAuthorHandler(Dish $dish)
	{
		if ($dish->getPreviousState() != $dish->getState() && $dish->hasBeenRefusedOrValidated()) {
			if ($dish->getAuthor()->getRoles()[0] == RoleEnum::EDITOR) {
				$author = $dish->getAuthor();
				$message = $this->generateMail($dish, "Ristaurante - Your dish had a change of state", $author->getUsername(), $author->getEmail());

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
			&& $dish->hasBeenRefusedOrValidated() && $dish->getState() == StateEnum::STATE_VALIDATED) {
			$waiters = $event->getObjectManager()->getRepository('DWBDSecurityBundle:User')->findByRoles(array('ROLE_WAITER'));
			$chunk = array_chunk($waiters, 10);

			foreach ($chunk as $emails) {
				$emails = array_map(function (User $user) {
					return $user->getEmail();
				}, $emails);
				$message = $this->generateMail($dish, "[INFO] - Ristaurante - A new dish is available", 'waiter', $emails);

				try {
					$this->mailer->send($message);
				} catch (\Exception $e) {
				}
			}
		}
	}

	/**
	 * @param Dish $dish
	 * @param string $subject
	 * @param string $user
	 * @param string|array $recipient
	 */
	private function generateMail(Dish $dish, $subject, $user, $recipient)
	{
		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($this->mailerUser)
			->setBody(
				$this->twig->render(
					'emails/entity-state-changed.html.twig',
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
