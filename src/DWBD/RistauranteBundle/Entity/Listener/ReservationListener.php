<?php

namespace DWBD\RistauranteBundle\Entity\Listener;


use Doctrine\ORM\Mapping\PostUpdate;
use DWBD\RistauranteBundle\Entity\Reservation;
use DWBD\RistauranteBundle\Entity\StateEnum;

class ReservationListener
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
