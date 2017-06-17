<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratorKfuCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:generator_kfu');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:generator_kfu started');

		$em    = $this->getContainer()->get('doctrine')->getManager('drug');
		$codes = $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->jsonForTree();

		# надо сгруппировать по родителю (запихпуть в list родителя дочерние)
		for ($i = 14; $i >= 5; $i = $i - 3) {
			foreach ($codes as $codeValue => &$code) {
				if (strlen($codeValue) == $i) {
					$key = substr($codeValue, 0, -3);
					if (isset($codes[$key])) {
						$codes[$key]['children'][] = $code;
						$codes[$key]['expanded']   = false;
					}
				}
			}
		}

		$grouped = array();

		foreach ($codes as $codeValue => $code) {
			if (strlen($codeValue) == 2) {
				$key           = $code['id'];
				$grouped[$key] = $code;
			}
		}

		$file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR . 'kfu.json';
		file_put_contents($file, json_encode($grouped));

		$output->writeln('+++ vidal:generator_kfu completed');
	}
}