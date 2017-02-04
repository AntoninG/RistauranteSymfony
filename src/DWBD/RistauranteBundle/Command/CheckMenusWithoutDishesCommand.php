<?php

namespace DWBD\RistauranteBundle\Command;


use Doctrine\ORM\Query\QueryException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
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
		}

		// We need to retrieve the admin users
		try  {
			$userRep = $this->getContainer()->get("doctrine")->getManager()->getRepository("DWBDSecurityBundle:User");
			$admins	 = $userRep->findByRole("ROLE_WAITER");
			if (!empty($admins)) {
				shuffle($admins);
				$admin = array_shift($admins);
				// TODO send mail
			} else {
				$output->writeln('None administrators to contact, here are the menus concerned :');
				foreach ($menus as $menu) {
					$output->writeln($menu->getId().' : '.$menu->getTitle().' (by '.$menu->getAuthor()->getLogin().')');
				}
			}
		}catch (QueryException $e) {
			$output->writeln($e->getMessage());
			return;
		}
	}
}