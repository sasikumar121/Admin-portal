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
class ProductMainCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:product_main')
            ->setDescription('Adds Product.MainID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        $output->writeln('--- vidal:product_main started');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager('drug');
        $pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET MainID = NULL")->execute();

        $productsByDocuments = $em->getRepository('VidalDrugBundle:Product')->productsByDocuments25();
        $updateQuery = $em->createQuery('
			UPDATE VidalDrugBundle:Product p
			SET p.MainID = :MainID
			WHERE p.ProductID = :ProductID
		');

        foreach ($productsByDocuments as $DocumentID => $products) {
            if (count($products) > 1) {
                # надо найти лучшего по ParentID, либо по посещаемости
                $mainProductID = null;

                foreach ($products as $product) {
                    if (!empty($product['ParentID'])) {
                        $mainProductID = $product['ParentID'];
                        break;
                    }
                }

                if ($mainProductID == null) {
                    $views = 0;
                    foreach ($products as $product) {
                        if ($product['ga_pageviews'] >= $views) {
                            $views = $product['ga_pageviews'];
                            $mainProductID = $product['ProductID'];
                        }
                    }
                }

                foreach ($products as $product) {
                    if ($product['ProductID'] != $mainProductID) {
                        $updateQuery->setParameter('MainID', $mainProductID);
                        $updateQuery->setParameter('ProductID', $product['ProductID']);
                        $updateQuery->execute();
                    }
                }
            }
        }

        $output->writeln("+++ vidal:product_main completed!");
    }
}