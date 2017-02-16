<?php

namespace DWBD\RistauranteBundle\Command;


use Doctrine\ORM\Query\QueryException;
use DWBD\SecurityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckMenusWithoutDishesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('app:check-menus')
			->setDescription('Check if there are menus without any dishes')
			->setHelp('This command check in the database if there are menus without any dishes. If at least one is found, an email is sent to administrators');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// Get menus without dishes
		$output->writeln("Search menus without dishes.");
		$menuRep = $this
			->getContainer()
			->get("doctrine")
			->getManager()
			->getRepository("DWBDRistauranteBundle:Menu");

		try {
			$menus = $menuRep->getMenusWithoutDishes();
		} catch (QueryException $e) {
			$output->writeln($e->getMessage());
			return;
		}

		// Nothing to do
		if (empty($menus)) {
			$output->writeln('No menus without dishes. End of task.');
			return;
		} else {
			$output->writeln(count($menus) . " menus found");
		}

		// We need to retrieve the admin users
		$userRep = $this->getContainer()->get("doctrine")->getManager()->getRepository("DWBDSecurityBundle:User");
		try {
			$admins = $userRep->findByRoles(array("ROLE_CHIEF", "ROLE_ADMIN"));
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
			$this->getContainer()->get('logger')->error($e->getMessage());
			$admins = array();
		}

		if (empty($admins)) {
			$output->writeln('None administrators to contact, here are the menus concerned :');
			foreach ($menus as $menu) {
				$output->writeln($menu->getId() . ' : ' . $menu->getTitle() . ' (by ' . $menu->getAuthor()->getUsername() . ')');
			}
			return;
		}

		$output->writeln("Mails will be send to administrators and chiefs (" . (count($admins)/* + count($chiefs)*/) . ")");
		$chunk = array_chunk($admins, 10);

		$sent = 0;
		foreach ($chunk as $array) {
			$emails = array_map(function (User $user) {
				return $user->getEmail();
			}, $array);

			$message = \Swift_Message::newInstance()
				->setSubject("[INFO] - Ristaurante - Menus without dishes")
				->setFrom($this->getContainer()->getParameter("mailer_user"))
				->setBody(
					$this->getContainer()->get("templating")->render(
						'emails/check-menus.html.twig',
						array('menus' => $menus)
					),
					'text/html'
				)
				->setTo($emails);

			$this->getContainer()->get("mailer")->send($message);
			++$sent;
		}

		$output->writeln($sent . ' mails sent');
	}
}
