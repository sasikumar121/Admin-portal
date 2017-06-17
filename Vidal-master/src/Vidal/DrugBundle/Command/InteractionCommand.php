<?php
namespace Vidal\DrugBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\Interaction;

class InteractionCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:interaction');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:interaction started');

		/** @var EntityManager $em */
		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		/** @var Interaction[] $interactions */
        $interactions = $em->getRepository("VidalDrugBundle:Interaction")->findAll();
        $total = count($interactions);
        $index = 0;

        foreach ($interactions as $interaction) {
            $index++;
            $output->writeln("... $index / $total : " . $interaction->getRusName());

            $input_string = $interaction->getText();
            $xml = new \DOMDocument();
            $source = mb_convert_encoding($input_string, 'HTML-ENTITIES', 'utf-8');
            @$xml->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $source);
            $link_list = $xml->getElementsByTagName('a');
            $link_list_length = $link_list->length;

            for ($i = 0; $i < $link_list_length; $i++) {
                $attributes = $link_list->item($i)->attributes;
                $href = $attributes->getNamedItem('href');
                $href_value = $href->value;

                if (!empty($href_value) && $href_value[0] == '/') {
                    $real_href = $this->get_redirect_url('localhost:97/app_dev.php' . $href_value);
                    $real_href = str_replace('/app_dev.php', '', $real_href);

                    if ($href_value != $real_href) {
                        $output->writeln('     ' . $href_value . ' -> ' . $real_href);
                        $href->value = $real_href;
                    }
                }
            }

            $output_string = $xml->saveHTML();
            $output_string = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $output_string);
            $output_string = str_replace('<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>', '', $output_string);

            $interaction->setText($output_string);
            $em->flush($interaction);
        }

		$output->writeln('+++ vidal:interaction completed');
	}

    function get_redirect_url($url){
        $redirect_url = null;

        $url_parts = @parse_url($url);
        if (!$url_parts) return false;
        if (!isset($url_parts['host'])) return false; //can't process relative URLs
        if (!isset($url_parts['path'])) $url_parts['path'] = '/';

        $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
        if (!$sock) return false;

        $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
        $request .= 'Host: ' . $url_parts['host'] . "\r\n";
        $request .= "Connection: Close\r\n\r\n";
        fwrite($sock, $request);
        $response = '';
        while(!feof($sock)) $response .= fread($sock, 8192);
        fclose($sock);

        if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
            if ( substr($matches[1], 0, 1) == "/" )
                return trim($matches[1]);
            else
                return trim($matches[1]);

        } else {
            return false;
        }
    }
}