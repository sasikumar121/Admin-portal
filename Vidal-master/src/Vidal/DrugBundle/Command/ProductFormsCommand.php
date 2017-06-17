<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда выставления множества описаний
 *
 * @package Vidal\DrugBundle\Command
 */
class ProductFormsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:product_forms')
            ->setDescription('Adds Product.forms');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        $output->writeln('--- vidal:product_forms started');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager('drug');
        $pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET forms = NULL")->execute();

        $products = $em->createQuery("
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatusID, p.ProductID, p.url,
				p.RusName, p.EngName, p.Name, p.ParentID, p.ga_pageviews, d.DocumentID, d.ArticleID, p.MainID,
				p.RegistrationNumber, p.RegistrationDate, i.RusName itemName, f.RusName formName,
				p.NonPrescriptionDrug, p.ProductTypeCode
			FROM VidalDrugBundle:Product p
			INNER JOIN p.document d
		    LEFT JOIN VidalDrugBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
		    LEFT JOIN VidalDrugBundle:ProductItem pi WITH pi.ProductID = p.ProductID
		    LEFT JOIN VidalDrugBundle:Item i WITH i.ItemID = pi.ItemID
		    LEFT JOIN VidalDrugBundle:Form f WITH f.FormID = i.FormID
			WHERE p.MarketStatusID IN (1,2,7)
				AND p.ProductTypeCode NOT IN ('SUBS')
				AND p.inactive = FALSE
				AND p.IsNotForSite = FALSE
				AND (p.ParentID IS NOT NULL OR p.MainID IS NOT NULL)
			ORDER BY p.RusName ASC
		")->getResult();

        $grouped = array();

        foreach ($products as $p) {
            $key = empty($p['ParentID']) ? $p['MainID'] : $p['ParentID'];
            if (!isset($grouped[$key])) {
                $grouped[$key] = array('products' => array());
            }
            $grouped[$key]['products'][] = $p;
        }

        foreach ($grouped as $ProductID => &$data) {
            $products = $data['products'];
            $mainProduct = $em->createQuery("
                SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatusID, p.ProductID, p.url,
                    p.RusName, p.EngName, p.Name, p.ParentID, p.ga_pageviews, d.DocumentID, d.ArticleID, p.MainID,
                    p.RegistrationNumber, p.RegistrationDate, i.RusName itemName, f.RusName formName,
                    p.NonPrescriptionDrug, p.ProductTypeCode
                FROM VidalDrugBundle:Product p
                INNER JOIN p.document d
                LEFT JOIN VidalDrugBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
                LEFT JOIN VidalDrugBundle:ProductItem pi WITH pi.ProductID = p.ProductID
		        LEFT JOIN VidalDrugBundle:Item i WITH i.ItemID = pi.ItemID
		        LEFT JOIN VidalDrugBundle:Form f WITH f.FormID = i.FormID
                WHERE p.ProductID = $ProductID
            ")->setMaxResults(1)->getOneOrNullResult();
            array_unshift($products, $mainProduct);

            $uniqZips = array();
            foreach ($products as $p) {
                $key = empty($p['formName']) ? trim($p['ZipInfo']) : $p['formName'];
                if (!isset($uniqZips[$key])) {
                    $uniqZips[$key] = array(
                        'ZipInfo' => empty($p['formName']) ? trim($p['ZipInfo']) : $p['formName'],
                        'MarketStatusID' => $p['MarketStatusID'],
                        'RegistrationDate' => trim( $p['RegistrationDate']),
                        'RegistrationNumber' => trim($p['RegistrationNumber']),
                        'NonPrescriptionDrug' => $p['NonPrescriptionDrug'],
                        'ProductTypeCode' => $p['ProductTypeCode'],
                        'ProductID' => $p['ProductID'],
                        'RusName' => $p['RusName'],
                    );
                }
            }
            $data['forms'] = array_values($uniqZips);
        }

        $updateQuery = $em->createQuery('
			UPDATE VidalDrugBundle:Product p
			SET p.forms = :forms
			WHERE p.ProductID = :ProductID
		');

        foreach ($grouped as $ProductID => $data) {
            $forms = json_encode($data['forms'], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            $updateQuery->setParameter('forms', $forms);
            $updateQuery->setParameter('ProductID', $ProductID);
            $updateQuery->execute();
        }

        $output->writeln("+++ vidal:product_forms completed!");
    }
}