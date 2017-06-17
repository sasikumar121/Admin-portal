<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Process\Process;
use Vidal\MainBundle\Entity\DeliveryLog;
use Vidal\MainBundle\Entity\Digest;

class DeliveryLogCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('vidal:delivery_log');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
	{
		/** @var Container $container */
		$container = $this->getContainer();
		/** @var EntityManager $em */
		$em = $container->get('doctrine')->getManager();
		/** @var Digest $digest */
		$digest = $em->getRepository('VidalMainBundle:Digest')->get();
		$pdo = $em->getConnection();
	}
}
