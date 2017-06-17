<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use
    Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Process\Process;

class ArtDashFixCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:art-dash-fix');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
        set_time_limit(0);

        $container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager('drug');
        $pdo = $em->getConnection();

        $pdo->prepare("UPDATE art SET link = REPLACE(link, '-–-','-')")->execute();
        $pdo->prepare("UPDATE art SET link = REPLACE(link, '–','-')")->execute();
        $pdo->prepare("UPDATE art SET link = REPLACE(link, '--','-')")->execute();
    }
}
