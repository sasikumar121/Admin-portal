<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда запуска ВСЕХ других команд, нужных для обновления базы данных
 */
class AllDocumentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('drug:all_document');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);
        $output->writeln("--- drug:all_document");

        $commandNames = array(
            'vidal:drop_non_rus',
            'vidal:product_document',
            'vidal:document_clphgrname',
            'vidal:document_composition',
            'vidal:document_image',
            'vidal:document_name',
            'vidal:document_empty',
            'vidal:atc_count',
            'vidal:info_count',
            'vidal:kfu_count',
            'vidal:nozology_count',
            'vidal:generator_atc',
            'vidal:generator_kfu',
            'vidal:generator_nozology',
        );

        foreach ($commandNames as $commandName) {
            $command = $this->getApplication()->find($commandName);
            $arguments = array('command' => $commandName);
            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        $output->writeln("+++ drug:all_document completed!");
    }
}