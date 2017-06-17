<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации нормальных имен для препаратов склеенных, по документу
 *
 * @package Vidal\DrugBundle\Command
 */
class ProductDocNameCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_doc_name')
			->setDescription('Adds Product.docEngName and Product docRusName');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_doc_name started');

		/** @var EntityManager $em */
		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET docEngName = NULL, docRusName = NULL")->execute();

        $pdo->prepare("
		    UPDATE product p
		    JOIN document d ON d.DocumentID = p.document_id
		    SET p.docEngName = d.EngName, p.docRusName = d.RusName
		    WHERE p.forms IS NOT NULL
		")->execute();

		$output->writeln("+++ vidal:product_doc_name completed!");
	}
}