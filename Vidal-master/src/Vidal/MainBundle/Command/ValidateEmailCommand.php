<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use
	Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface,
	Symfony\Component\Process\Process;

class ValidateEmailCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:validate_email');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);
		$output->writeln('--- vidal:validate_email started');

		$container = $this->getContainer();
		/** @var EntityManager $em */
		$em = $container->get('doctrine')->getManager();
		$pdo = $em->getConnection();
		$stmtValid = $pdo->prepare('UPDATE user SET emailValidated=1 WHERE username = ?');
		$stmtNotValid = $pdo->prepare('UPDATE user SET emailValidated=0 WHERE username = ?');

		$users = $em->createQuery('
			SELECT u.id, u.username
			FROM VidalMainBundle:User u
			ORDER BY u.id ASC
		')->getResult();

		foreach ($users as $user) {
			$email = $user['username'];

			if ($this->validateEmail($email)) {
				$stmtValid->bindParam(1, $email);
				$stmtValid->execute();
			}
			else {
				$stmtNotValid->bindParam(1, $email);
				$stmtNotValid->execute();
			}
		}

		$output->writeln('+++ vidal:validate_email completed!');
	}

	/**
	 * @param $email
	 * @param string $recType
	 * @return bool
	 */
	private function validateEmail($email, $recType = '')
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		$hostName = $this->getDomainFromEmail($email);

		if (!empty($hostName)) {
			if ($recType == '') {
				$recType = "MX";
			}
			exec("nslookup -type=$recType $hostName", $result);
			// check each line to find the one that starts with the host
			// name. If it exists then the function succeeded.
			foreach ($result as $line) {
				if (eregi("^$hostName", $line)) {
					return true;
				}
			}
			// otherwise there was no mail handler for the domain
			return false;
		}

		return false;
	}

	private function getDomainFromEmail($email)
	{
		// Get the data after the @ sign
		$domain = substr(strrchr($email, "@"), 1);

		return $domain;
	}
}
