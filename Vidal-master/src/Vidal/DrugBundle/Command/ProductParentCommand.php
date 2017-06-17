<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда выставления основного продукта из группы похожих
 *
 * @package Vidal\DrugBundle\Command
 */
class ProductParentCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_parent')
			->setDescription('Adds Product.parent_id');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_parent started');

        /** @var EntityManager $em */
		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET parent_id = ParentID")->execute();

		$output->writeln("+++ vidal:product_parent completed!");
	}
}