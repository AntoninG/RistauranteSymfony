<?php

namespace DWBD\SecurityBundle\Command;

use DWBD\SecurityBundle\Entity\Enum\RoleEnum;
use DWBD\SecurityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class CreateUserCommand
 * Allows to create a user with his username, password, email and role
 *
 * @package DWBD\SecurityBundle\Command
 */
class CreateUserCommand extends ContainerAwareCommand
{
	private static $allowedRoles;
	private static $defaultRole = "ROLE_WAITER";

	protected function configure()
	{
		self::$allowedRoles = RoleEnum::getRoles();
		$this
			->setName('security:create-user')
			->setDescription('Create new user.')
			->setHelp('This command allows you to create one user...' . PHP_EOL .
				'Syntax : security:create-user username password email role')
			->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
			->addArgument('password', InputArgument::REQUIRED, 'The password of the user')
			->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
			->addArgument('role', InputArgument::OPTIONAL, 'The role of the user.', self::$defaultRole);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');
		$username = $input->getArgument('username');
		$password = $input->getArgument('password');
		$email = $input->getArgument('email');
		$role = $input->getArgument('role');

		if (!in_array($role, self::$allowedRoles)) {
			$output->writeln('The role you choose is not allowed.');
			$question = new ConfirmationQuestion("Continue with default role : " . self::$defaultRole . " ?", false);

			if (!$helper->ask($input, $output, $question)) {
				return;
			}
		}

		$question = new ConfirmationQuestion('All is fine, would you like to create the user ? [y/n]', false);
		if ($helper->ask($input, $output, $question)) {
			$encoder = $this->getContainer()->get('security.password_encoder');
			$user = new User();
			$hash = $encoder->encodePassword($user, $password);
			$user
				->setUsername($username)
				->setPassword($hash)
				->setEmail($email)
				->setRoles([$role]);

			$manager = $this->getContainer()->get('doctrine')->getManager();
			$manager->persist($user);

			try {
				$manager->flush();
			} catch (Exception $exception) {
				$output->writeln($exception->getMessage());
				return;
			}
		}
	}
}
