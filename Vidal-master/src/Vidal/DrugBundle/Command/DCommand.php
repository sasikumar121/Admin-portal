<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\InfoPage;

class DCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:d');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        $output->writeln('--- vidal:d');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager('drug');

        $file = fopen(__DIR__ . '/Data/document-year.csv', 'r');
        $i = 0;

        while (($line = fgetcsv($file)) !== FALSE) {
            $i++;
            list($DocumentID, $year) = explode(';', $line[0]);

            if ($year == '2017') {
                $em->createQuery("UPDATE VidalDrugBundle:Document d SET d.YearEdition = '2017' WHERE d.DocumentID = :DocumentID")
                    ->setParameter('DocumentID', $DocumentID)
                    ->execute();

                $output->writeln ('... 2017 for DocumentID ' . $DocumentID);
            }
        }

        fclose($file);

        $output->writeln("+++ vidal:d completed!");
    }
}