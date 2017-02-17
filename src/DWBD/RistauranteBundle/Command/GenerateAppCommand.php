<?php

namespace DWBD\RistauranteBundle\Command;


use DWBD\RistauranteBundle\Entity\Dish;
use DWBD\RistauranteBundle\Entity\Enum\StateEnum;
use DWBD\RistauranteBundle\Entity\Menu;
use DWBD\RistauranteBundle\Entity\Reservation;
use DWBD\SecurityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateAppCommand extends ContainerAwareCommand
{
	private $prefix = 'Ristaurante';

	protected function configure()
	{
		$this->setName('app:generate')
			->setDescription('Generate : ' . PHP_EOL .
				'4 users (one for each role)' . PHP_EOL .
				'4 menus' . PHP_EOL . '4 dishes ' . PHP_EOL . '2 reservations'
			)
			->setHelp($this->getDescription() . PHP_EOL .
				'No parameters are required, and all users are prefixed by Ristaurante{Role}.' . PHP_EOL .
				'Username and passwords are the same.'
			);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine')->getManager();

		$output->writeln('Start generating users');
		$users = $this->generateUsers();
		$output->writeln('Here the 4 users generated (username and passwords are the same) :');
		foreach ($users as $user) {
			$output->writeln($user->getUsername() . ' ' . $user->getRoles()[0] . ' ' . $user->getEmail());
			$em->persist($user);
		}
		try {
			$em->flush();
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
			$this->getContainer()->set('doctrine.orm.entity_manager', null);
			$this->getContainer()->set('doctrine.orm.default_entity_manager', null);
			$em = $this->getContainer()->get('doctrine')->getManager();
		}

		$output->writeln(PHP_EOL);

		$output->writeln('Start generating reservations');
		$reservations = $this->generateBooks();
		$output->writeln('Here the two reservations generated :');
		foreach ($reservations as $r) {
			$output->writeln($r->getDate()->format('d/m/Y') . ' ' . $r->getTime()->format('H:i') . ' for ' . $r->getNumber() . ' people');
			$em->persist($r);
		}
		try {
			$em->flush();
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
			$this->getContainer()->set('doctrine.orm.entity_manager', null);
			$this->getContainer()->set('doctrine.orm.default_entity_manager', null);
			$em = $this->getContainer()->get('doctrine')->getManager();
		}

		$output->writeln(PHP_EOL);

		$output->writeln('Start generating dishes');
		$dishes = $this->generateDishes($users['editor']);
		$output->writeln('Here the four dishes generated :');
		foreach ($dishes as $d) {
			$output->writeln($d->getTitle() . ', homemade : ' . $d->isHomemade() . ', ' . $d->getPrice() . ' EUR, ' . StateEnum::getStatesTranslation()[$d->getState()]);
			$em->persist($d);
		}
		try {
			$em->flush();
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
			$this->getContainer()->set('doctrine.orm.entity_manager', null);
			$this->getContainer()->set('doctrine.orm.default_entity_manager', null);
			$em = $this->getContainer()->get('doctrine')->getManager();
		}

		$output->writeln(PHP_EOL);

		$output->writeln('Start generating menus');
		$menus = $this->generateMenus($users['editor'], $dishes[3]);
		$output->writeln('Here the four menus generated :');
		foreach ($menus as $m) {
			$output->writeln($m->getTitle() . ', ' . $m->getPrice() . ' EUR, ' . StateEnum::getStatesTranslation()[$m->getState()]);
			$em->persist($m);
		}
		try {
			$em->flush();
		} catch (\Exception $e) {
			$output->writeln($e->getMessage());
		}
	}

	private function generateMenus(User $author, Dish $dish)
	{
		$menus = array();

		$menu1 = new Menu();
		$menus[] = $menu1->addDish($dish)->setPrice(10.99)->setState(StateEnum::STATE_DRAFT)->setAuthor($author)->setTitle($this->prefix . ' menu 1')->setDisplayOrder(45);
		$menu2 = new Menu();
		$menus[] = $menu2->setPrice(10.99)->setState(StateEnum::STATE_WAITING)->setAuthor($author)->setTitle($this->prefix . ' menu 2')->setDisplayOrder(4);
		$menu3 = new Menu();
		$menus[] = $menu3->addDish($dish)->setPrice(10.99)->setState(StateEnum::STATE_REFUSED)->setAuthor($author)->setTitle($this->prefix . ' menu 3')->setDisplayOrder(10);
		$menu4 = new Menu();
		$menus[] = $menu4->setPrice(10.99)->setState(StateEnum::STATE_VALIDATED)->setAuthor($author)->setTitle($this->prefix . ' menu 4')->setDisplayOrder(1);

		return $menus;
	}

	private function generateDishes(User $author)
	{
		$dishes = array();

		$dish1 = new Dish();
		$dishes[] = $dish1->setTitle($this->prefix . ' dish 1')->setAuthor($author)->setHomemade(false)->setState(StateEnum::STATE_DRAFT)->setPrice(10.99)->setDescription($dish1->getTitle());
		$dish2 = new Dish();
		$dishes[] = $dish2->setTitle($this->prefix . ' dish 2')->setAuthor($author)->setHomemade(true)->setState(StateEnum::STATE_WAITING)->setPrice(10.99)->setDescription($dish1->getTitle());
		$dish3 = new Dish();
		$dishes[] = $dish3->setTitle($this->prefix . ' dish 3')->setAuthor($author)->setHomemade(false)->setState(StateEnum::STATE_REFUSED)->setPrice(10.99)->setDescription($dish1->getTitle());
		$dish4 = new Dish();
		$dishes[] = $dish4->setTitle($this->prefix . ' dish 4')->setAuthor($author)->setHomemade(true)->setState(StateEnum::STATE_VALIDATED)->setPrice(10.99)->setDescription($dish1->getTitle());

		return $dishes;
	}

	private function generateBooks()
	{
		$books = array();

		$book1 = new Reservation();
		$books[] = $book1->setEmail('antonin.guilet-dupont@laposte.net')->setName('Antonin GUILET')->setDate(\DateTime::createFromFormat('d/m/Y', '18/06/2016'))->setTime(\DateTime::createFromFormat('H:i', '12:30'))->setNumber(1)->setPhone('0606060606');
		$book2 = new Reservation();
		$books[] = $book2->setEmail('antonin1111@hotmail.fr')->setName('Rose-Marie DUPONT')->setDate(\DateTime::createFromFormat('d/m/Y', '18/06/2016'))->setTime(\DateTime::createFromFormat('H:i', '21:00'))->setNumber(11)->setPhone('0606060606');

		return $books;
	}

	private function generateUsers()
	{
		$encoder = $this->getContainer()->get('security.password_encoder');
		$users = array();

		$waiter = new User();
		$users['waiter'] = $waiter->setRoles(array('ROLE_WAITER'))->setUsername($this->prefix . 'Waiter')->setEmail('waiter@waiter.com')->setPassword($encoder->encodePassword($waiter, $waiter->getUsername()));
		$editor = new User();
		$users['editor'] = $editor->setRoles(array('ROLE_EDITOR'))->setUsername($this->prefix . 'Editor')->setEmail('editor@editor.com')->setPassword($encoder->encodePassword($editor, $editor->getUsername()));
		$reviewer = new User();
		$users['reviewer'] = $reviewer->setRoles(array('ROLE_REVIEWER'))->setUsername($this->prefix . 'Reviewer')->setEmail('reviewer@reviewer.com')->setPassword($encoder->encodePassword($reviewer, $reviewer->getUsername()));
		$chief = new User();
		$users['chief'] = $chief->setRoles(array('ROLE_CHIEF'))->setUsername($this->prefix . 'Chief')->setEmail('chief@chief.com')->setPassword($encoder->encodePassword($chief, $chief->getUsername()));
		$admin = new User();
		$users['admin'] = $admin->setRoles(array('ROLE_ADMIN'))->setUsername($this->prefix . 'Admin')->setEmail('admin@admin.com')->setPassword($encoder->encodePassword($admin, $admin->getUsername()));

		return $users;

	}
}