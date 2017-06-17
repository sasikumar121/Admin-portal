<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @package Vidal\DrugBundle\Command
 */
class ProductPicturesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_pictures')
			->setDescription('Formats product.pictures');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_pictures started');

        /** @var EntityManager $em */
		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET pictures = NULL")->execute();
        $pdo->prepare("UPDATE product SET countPictures = NULL")->execute();

        $stmt = $pdo->prepare("
			SELECT pp.filename, p.ProductID, p.parent_id, p.MainID
			FROM productpicture pp
			INNER JOIN product p ON p.ProductID = pp.ProductID
			WHERE pp.filename IS NOT NULL
				AND p.MarketStatusID IN (1,2,7)
				AND p.ProductTypeCode NOT IN ('SUBS')
				AND p.inactive = FALSE
				AND p.IsNotForSite = FALSE
				AND pp.YearEdition IN ('2016', '2017')
		");

        $stmt->execute();
        $results = $stmt->fetchAll();
		$products = array();
		$updateQuery = $em->createQuery("UPDATE VidalDrugBundle:Product p SET p.pictures = :pictures, p.countPictures = :countPictures WHERE p.ProductID = :ProductID");

		foreach ($results as $pp) {
			if (!empty($pp['parent_id'])) {
				$key = $pp['parent_id'];
			}
			elseif (!empty($pp['MainID'])) {
				$key = $pp['MainID'];
			}
			else {
				$key = $pp['ProductID'];
			}

			if (!isset($products[$key])) {
				$products[$key] = array();
			}

			$products[$key][] = $pp['filename'];
        }

		foreach ($products as $ProductID => $pictures) {
			$pictures = array_unique($pictures);
            $countPictures = count($pictures);
			$pictures = implode('|', $pictures);
			$updateQuery->setParameter('pictures', $pictures);
			$updateQuery->setParameter('ProductID', $ProductID);
			$updateQuery->setParameter('countPictures', $countPictures);
			$updateQuery->execute();
		}

        $output->writeln("+++ vidal:product_pictures completed!");
	}
}