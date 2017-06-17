<?php
namespace Vidal\MainBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Vidal\MainBundle\Entity\User;

class TryCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:try');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);
		ini_set('max_execution_time', 0);
		ini_set('max_input_time', 0);
		ini_set('memory_limit', -1);

		$container = $this->getContainer();
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var User $user */
        $user = $em->getRepository('VidalMainBundle:User')->findOneByUsername('_stranger@mail.ru');

        $em->getRepository('VidalMainBundle:User')->setDeliveryLogFailed($user);
	}
}
