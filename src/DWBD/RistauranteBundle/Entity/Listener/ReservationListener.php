<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\ORM\Mapping\PostUpdate;
use DWBD\RistauranteBundle\Entity\Reservation;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;

/**
 * Class ReservationListener
 * This is an EntityListener for Reservation entity
 * It manages all mails related to the reservation
 *
 * @package DWBD\RistauranteBundle\Entity\Listener
 */
class ReservationListener
{
	/** @var \Swift_Mailer */
	private $mailer;

	/** @var \Twig_Environment */
	private $twig;

	/** @var  string */
	private $mailerUser;

	/**
	 * ReservationListener constructor.
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
	 * @param Reservation $reservation
	 */
	public function preventConsumerHandler(Reservation $reservation)
	{
		$message = \Swift_Message::newInstance()
			->setSubject("Ristaurante - Some news about your reservation")
			->setFrom($this->mailerUser)
			->setBody(
				$this->twig->render(
					'emails/reservation-state-change.html.twig',
					array(
						'reservation' => $reservation,
						'refused' => $reservation->getState() == StateEnum::STATE_REFUSED,
					)
				),
				'text/html'
			)
			->setTo($reservation->getEmail());

		try {
			$this->mailer->send($message);
		} catch (\Exception $e) {
		}
	}
}
