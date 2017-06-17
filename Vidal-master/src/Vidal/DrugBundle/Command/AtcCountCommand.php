<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AtcCountCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:atc_count');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$output->writeln('--- vidal:atc_count started');

		$raw         = $em->getRepository('VidalDrugBundle:ATC')->countProducts();
		$updateQuery = $em->createQuery('
			UPDATE VidalDrugBundle:ATC a
			SET a.countProducts = :countProducts
			WHERE a.ATCCode = :ATCCode
		');

		# ставим сколько всего у них препаратов
		for ($i = 0; $i < count($raw); $i++) {
			$updateQuery->setParameters(array(
				'countProducts' => $raw[$i]['countProducts'],
				'ATCCode'       => $raw[$i]['ATCCode'],
			))->execute();
		}

		$output->writeln('+++ vidal:atc_count completed');
	}
}