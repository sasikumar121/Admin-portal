<?php
namespace Vidal\VeterinarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации нормальных дат препаратов
 *
 * @package Vidal\DrugBundle\Command
 */
class ProductRegistrationDateCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('veterinar:product_registration_date')
			->setDescription('Transforms dates of product registration to NULL if its like 0000-00-00 00:00:00');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- veterinar:product_registration_date started');

		$em = $this->getContainer()->get('doctrine')->getManager('veterinar');

		$em->createQuery('
			UPDATE VidalVeterinarBundle:Product p
			SET p.RegistrationDate = NULL
			WHERE p.RegistrationDate = \'0000-00-00 00:00:00\'
		')->execute();

		$em->createQuery('
			UPDATE VidalVeterinarBundle:Product p
			SET p.DateOfCloseRegistration = NULL
			WHERE p.DateOfCloseRegistration = \'0000-00-00 00:00:00\'
		')->execute();

		#########################
		$products = $em->createQuery('
			SELECT p.ProductID, p.RegistrationDate, p.DateOfCloseRegistration
			FROM VidalVeterinarBundle:Product p
		')->getResult();

		$updateQuery = $em->createQuery('
			UPDATE VidalVeterinarBundle:Product p
			SET p.RegistrationDate = :reg
			WHERE p.ProductID = :id
		');

		for ($i = 0; $i < count($products); $i++) {
			$date = $products[$i]['RegistrationDate'];

			if ($date instanceof \DateTime) {
				$year  = intval($date->format('d')) + 2000;
				$month = intval($date->format('m'));
				$day   = intval($date->format('y'));
				$date->setDate($year, $month, $day);

				$updateQuery
					->setParameters(array(
						'reg' => $date->format('Y-m-d 00:00:00'),
						'id'  => $products[$i]['ProductID'],
					))
					->execute();
			}

			if ($i && $i % 500 == 0) {
				$output->writeln("... +$i");
			}
		}

		$updateQuery = $em->createQuery('
			UPDATE VidalVeterinarBundle:Product p
			SET p.DateOfCloseRegistration = :reg
			WHERE p.ProductID = :id
		');

		for ($i = 0; $i < count($products); $i++) {
			$date = $products[$i]['DateOfCloseRegistration'];

			if ($date instanceof \DateTime) {
				$year  = intval($date->format('d')) + 2000;
				$month = intval($date->format('m'));
				$day   = intval($date->format('y'));
				$date->setDate($year, $month, $day);

				$updateQuery
					->setParameters(array(
						'reg' => $date->format('Y-m-d 00:00:00'),
						'id'  => $products[$i]['ProductID'],
					))
					->execute();
			}

			if ($i && $i % 500 == 0) {
				$output->writeln("... +$i");
			}
		}

		$output->writeln('+++ veterinar:product_registration_date completed!');
	}
}