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
		try {
			$queryBuilder = $this
				->getContainer()
				->get("doctrine")
				->getManager()
				->getRepository("DWBDRistauranteBundle:Menu");
			/*
				->createQueryBuilder('m');

			$queryJoin = $queryBuilder
				->select('m.id')
				->innerJoin('m.dishes', 'd')
				->getQuery()
				->getArrayResult();

			$query = $queryBuilder
				->select(['m.id', 'm.title'])
				->where($queryBuilder->expr()->notIn('m.id', ':queryJoin'))
				->setParameter(':queryJoin', $queryJoin)
				->getQuery();

			$result = $query->getResult();

			$output->writeln($query->getDQL());*/

			$menus = $queryBuilder->getMenusWithoutDishes();
			foreach ($menus as $menu) {
				$output->writeln($menu->getTitle());
			}

			//TODO retrieve admin and send mail
		} catch (QueryException $e) {
			$output->writeln($e->getMessage());
		}
	}
}