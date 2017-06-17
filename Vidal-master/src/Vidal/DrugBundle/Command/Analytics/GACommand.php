<?php
namespace Vidal\DrugBundle\Command\Analytics;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GACommand extends ContainerAwareCommand
{
    /** @var \Google_Client */
    protected $client;
    /** @var \Google_Service_Analytics */
    protected $analytics;

    protected $analyticsViewId = 'ga:78472229';
    protected $startDate = '2016-04-22';
    protected $endDate = '2017-04-22';
    protected $metrics = 'ga:pageviews';

    protected function configure()
    {
        $this->setName('vidal:product_analytics')->setDescription('Google Analitics for Product via API');
    }

    private function findPageData($productUrl)
    {
        $data = $this->analytics->data_ga->get($this->analyticsViewId, $this->startDate, $this->endDate, $this->metrics, array(
            'dimensions' => 'ga:pagePath',
            'filters' => 'ga:pagePath==' . $productUrl
        ));

        return $data->getRows();
    }

    function initializeAnalytics()
    {
        $KEY_FILE_LOCATION = __DIR__ . '/ga.json';

        $client = new \Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $client->addScope('https://www.googleapis.com/auth/webmasters.readonly');
        $client->addScope('https://www.googleapis.com/auth/webmasters');

        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setHttpClient($guzzleClient);

        $this->client = $client;
        $this->analytics = new \Google_Service_Analytics($this->client);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $output->writeln('--- vidal:ga started');
        $this->initializeAnalytics();

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager('drug');
        $pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET ga_pageviews = NULL")->execute();
        $products = $em->getRepository('VidalDrugBundle:Product')->findGa();

        $updateQuery = $em->createQuery('
			UPDATE VidalDrugBundle:Product p
			SET p.ga_pageviews = :pageviews
			WHERE p.ProductID = :ProductID
		');
        $productsData = array();

        foreach ($products as &$product) {
            $href = empty($product['url'])
                ? "/drugs/{$product['Name']}__{$product['ProductID']}"
                : "/drugs/{$product['url']}";
            $product['href'] = $href;
            $productsData[$href] = $product;
        }

        $max = 30;
        $chunked = array_chunk($products, $max);
        $total = count($products);
        $i = 1;

        foreach ($chunked as $chunk) {
            $output->writeln('... ' . ($i * $max) . ' / ' . $total);
            $hrefs = array();

            try {
                foreach ($chunk as $product) {
                    $hrefs[] = str_replace(',', '\,', $product['href']);
                }
                $filters = 'ga:pagePath==' . implode(',ga:pagePath==', $hrefs);

                $result = $this->analytics->data_ga->get($this->analyticsViewId, $this->startDate, $this->endDate, $this->metrics, array(
                    'dimensions' => 'ga:pagePath',
                    'filters' => $filters,
                ));
                $rows = $result->getRows();

                if (count($rows) != count($hrefs)) {
                    $output->writeln("count rows: " . count($rows) . '. count hrefs: ' . count($hrefs));
                }

                foreach ($rows as $row) {
                    $href = $row[0];
                    $pageviews = $row[1];
                    $product = $productsData[$href];

                    $updateQuery->setParameter('ProductID', $product['ProductID']);
                    $updateQuery->setParameter('pageviews', $pageviews);
                    $updateQuery->execute();
                }
            }
            catch (\Exception $e) {
                $output->writeln($e->getMessage());
                var_dump($hrefs);
                exit;
            }

            $i++;
        }

        $output->writeln('+++ vidal:product_analytics completed');
    }
}