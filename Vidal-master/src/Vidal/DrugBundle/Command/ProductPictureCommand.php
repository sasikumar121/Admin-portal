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
class ProductPictureCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_picture')
			->setDescription('Formats product_picture');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_picture started');

        /** @var EntityManager $em */
		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo = $em->getConnection();

        # выставляем путь к файлу ЕСЛИ файл найден в системе
        $pdo->prepare("UPDATE picture SET filename = NULL, found = NULL")->execute();
        $stmt = $pdo->prepare("SELECT PictureID, PathForBookEdition, PathForElectronicEdition FROM picture");
        $stmt->execute();
        $results = $stmt->fetchAll();

        $updatePicture = $em->createQuery("UPDATE VidalDrugBundle:Picture p SET p.filename = :filename, p.found = :found WHERE p.PictureID = :PictureID");
        $updateProductPicture = $em->createQuery("UPDATE VidalDrugBundle:ProductPicture p SET p.filename = :filename, p.found = :found WHERE p.PictureID = :PictureID");

        foreach ($results as $p) {
            $filename = preg_replace('/.+\\\\JPG\\\\/', '', $p['PathForElectronicEdition']);
            $filename = strtolower($filename);
            $uploadDir = $this->getContainer()->getParameter('upload_dir');
            $filepath = $uploadDir . '/products/' . $filename;
            $found = file_exists($filepath);

            $updatePicture
                ->setParameter('filename', $filename)
                ->setParameter('found', $found)
                ->setParameter('PictureID', $p['PictureID'])
                ->execute();

            $updateProductPicture
                ->setParameter('filename', $filename)
                ->setParameter('found', $found)
                ->setParameter('PictureID', $p['PictureID'])
                ->execute();
        }

        # Выставляем правильную дату
        $pdo->prepare("UPDATE productpicture SET DateEditFormatted = NULL")->execute();
        $stmt = $pdo->prepare("SELECT DateEdit, ProductID, PictureID FROM productpicture WHERE DateEdit IS NOT NULL");
        $stmt->execute();
        $results = $stmt->fetchAll();

        $updateQuery = $em->createQuery("UPDATE VidalDrugBundle:ProductPicture p SET p.DateEditFormatted = :DateEditFormatted WHERE p.ProductID = :ProductID AND p.PictureID = :PictureID");

        foreach ($results as $pp) {
            $date = $pp['DateEdit'];
            $dateParts = explode('.', $date);
            $year = intval($dateParts[2]);
            $month = $dateParts[1];
            $day = $dateParts[0];
            if ($year < 2000) {
                $year = 2000 + $year;
            }
            $DateEdit = implode('.', array($day, $month, $year));
            $DateEdit = new \DateTime($DateEdit);
            $DateEdit = $DateEdit->format('Y-m-d');
            $updateQuery
                ->setParameter('DateEditFormatted', $DateEdit)
                ->setParameter('ProductID', $pp['ProductID'])
                ->setParameter('PictureID', $pp['PictureID'])
                ->execute();
        }

        # выставляем productpicture
        $results = $em->createQuery("SELECT p.ProductID, p.PictureID FROM VidalDrugBundle:ProductPicture p")->getResult();
        $updateQuery = $em->createQuery("UPDATE VidalDrugBundle:ProductPicture p SET p.productpicture = :productpicture WHERE p.ProductID = :ProductID AND p.PictureID = :PictureID");

        foreach ($results as $pp) {
            $productpicture = $pp['ProductID'] . '+' . $pp['PictureID'];
            $updateQuery
                ->setParameter('productpicture', $productpicture)
                ->setParameter('ProductID', $pp['ProductID'])
                ->setParameter('PictureID', $pp['PictureID'])
                ->execute();
        }

        $output->writeln("+++ vidal:product_picture completed!");
	}
}