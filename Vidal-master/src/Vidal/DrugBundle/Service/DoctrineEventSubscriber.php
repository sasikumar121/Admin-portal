<?php

namespace Vidal\DrugBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Vidal\DrugBundle\Entity\Ads;
use Vidal\DrugBundle\Entity\AdsSlider;
use Vidal\DrugBundle\Entity\Article;
use Vidal\DrugBundle\Entity\Art;
use Vidal\DrugBundle\Entity\Publication;
use Vidal\DrugBundle\Entity\Product;
use Vidal\DrugBundle\Entity\Document;
use Vidal\DrugBundle\Entity\PharmPortfolio;
use Vidal\DrugBundle\Entity\Tag;
use Vidal\DrugBundle\Entity\InfoPage;

class DoctrineEventSubscriber implements EventSubscriber
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Возвращает список имён событий, которые обрабатывает данный класс. Callback-методы должны иметь такие же имена
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postPersist',
            'preUpdate',
            'postUpdate',
            'preRemove',
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        # проставляем ссылку, если пустая
        if ($entity instanceof Article || $entity instanceof Art) {
            $this->setLink($entity);
        }

        if ($entity instanceof Publication) {
            $this->checkDuplicateTitle($args, 'publication');
        }
        elseif ($entity instanceof Article) {
            $this->checkDuplicateTitle($args, 'article');
        }
        elseif ($entity instanceof Art) {
            $this->checkDuplicateTitle($args, 'art');
        }
        elseif ($entity instanceof Document) {
            $this->checkDuplicateDocument($args);
        }
        elseif ($entity instanceof Product) {
            $this->checkDuplicateProduct($args);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        # проставляем мета к видео, если его загрузили
        if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication
            || $entity instanceof PharmPortfolio || $entity instanceof Ads || $entity instanceof AdsSlider
        ) {
            $this->setVideoMeta($entity);
        }

        if ($entity instanceof Product) {
            $this->autocompleteProduct($entity);
            $this->updateCountProducts($args);
            $this->updateProductNames($args);
            $this->updateProductUrl($args);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        # проставляем ссылку, если пустая
        if ($entity instanceof Article || $entity instanceof Art) {
            $this->setLink($entity);
        }

        if ($entity instanceof InfoPage) {
            if ($tag = $entity->getTag()) {
                $pdo = $args->getEntityManager()->getConnection();
                $pdo->prepare("UPDATE tag SET InfoPageID = NULL WHERE InfoPageID = {$entity->getInfoPageID()}")->execute();
                $pdo->prepare("UPDATE tag SET InfoPageID = {$entity->getInfoPageID()} WHERE id = {$tag->getId()}")->execute();
            }
        }

        if ($entity instanceof Tag) {
            if ($infoPage = $entity->getInfoPage()) {
                $pdo = $args->getEntityManager()->getConnection();
                $pdo->prepare("UPDATE infopage SET tag_id = NULL WHERE tag_id = {$entity->getId()}")->execute();
                $pdo->prepare("UPDATE infopage SET tag_id = {$entity->getId()} WHERE InfoPageID = {$infoPage->getInfoPageID()}")->execute();
            }
        }

        if ($entity instanceof Product) {
            $this->autocompleteProduct($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        # проставляем мета к видео, если его загрузили
        if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication
            || $entity instanceof PharmPortfolio || $entity instanceof Ads || $entity instanceof AdsSlider
        ) {
            $this->setVideoMeta($entity);
        }

        # проставляем сколько всего связей у тегов (Tag.total)
        if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication) {
            $tagService = $this->container->get('drug.tag_total');
            foreach ($entity->getTags() as $tag) {
                $tagService->count($tag->getId());
            }
            foreach ($entity->getInfoPages() as $ip) {
                if ($tag = $ip->getTag()) {
                    $tagService->count($tag->getId());
                }
            }
        }

        if ($entity instanceof Product) {
            $this->updateProductUrl($args);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Product) {
            $em = $args->getEntityManager();
            $pdo = $em->getConnection();
            $stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
            $stmt->execute();
            $stmt = $pdo->prepare('DELETE FROM product WHERE ProductID = ' . $entity->getProductID());
            $stmt->execute();
        }

        if ($entity instanceof InfoPage) {
            $em = $args->getEntityManager();
            $pdo = $em->getConnection();
            $stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
            $stmt->execute();
            $stmt = $pdo->prepare('DELETE FROM tag WHERE InfoPageID = ' . $entity->getInfoPageID());
            $stmt->execute();
        }

        if ($entity instanceof \Vidal\DrugBundle\Entity\ProductCompany) {
            $em = $args->getEntityManager();
            $pdo = $em->getConnection();
            $stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
            $stmt->execute();
            $stmt = $pdo->prepare('DELETE FROM product_company WHERE ProductID = ' . $entity->getProductID()->getProductID() . ' AND CompanyID = ' . $entity->getCompanyID()->getCompanyID());
            $stmt->execute();
        }
    }

    private function setVideoMeta($entity)
    {
        $video = $entity->getVideo();

        if ($video && isset($video['path'])) {
            $rootDir = $this->container->get('kernel')->getRootDir() . '/../';
            require_once $rootDir . 'src/getID3/getid3.php';

            $getID3 = new \getID3;
            $filename = $rootDir . 'web' . $video['path'];
            $file = $getID3->analyze($filename);

            $entity->setVideoWidth($file['video']['resolution_x']);
            $entity->setVideoHeight($file['video']['resolution_y']);
            $this->container->get('doctrine')->getManager('drug')->flush($entity);
        }
    }

    private function setLink($entity)
    {
        $link = $entity->getLink();

        if (empty($link)) {
            $link = $this->translit($entity->getTitle());
            $entity->setLink($link);
        }
    }

    private function translit($text)
    {
        $pat = array('/&[a-z]+;/', '/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
        $rep = array('', '$1', '$1');
        $text = preg_replace($pat, $rep, $text);
        $text = mb_strtolower($text, 'utf-8');

        // Русский алфавит
        $rus_alphabet = array(
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
            'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
            'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
            'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
            'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
            ' ', '.', '(', ')', ',', '/', '?'
        );

        // Английская транслитерация
        $rus_alphabet_translit = array(
            'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'Y',
            'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
            'H', 'TS', 'CH', 'SH', 'SCH', '', 'Y', '', 'E', 'YU', 'IA',
            'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y',
            'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
            'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ia',
            '-', '-', '-', '-', '-', '-', '-'
        );

        return str_replace($rus_alphabet, $rus_alphabet_translit, $text);
    }

    private function autocompleteProduct($product)
    {
        try {
            # check product is visible
            $productTypes = array('DRUG', 'GOME');
            $marketIds = array(1, 2, 7);
            if ($product->getInactive()
                || !in_array($product->getProductTypeCode(), $productTypes)
                || !in_array($product->getMarketStatusID()->getMarketStatusID(), $marketIds)
            ) {
                return false;
            }

            # get names
            $RusName = $this->strip($product->getRusName());
            $RusName = mb_strtolower($RusName, 'UTF-8');
            $EngName = $this->strip($product->getEngName());
            $EngName = mb_strtolower($EngName, 'UTF-8');

            $elasticaClient = new \Elastica\Client();
            $elasticaIndex = $elasticaClient->getIndex('website');

            # check if names exists
            $client = new \Elasticsearch\Client();

            $s['index'] = 'website';
            $s['type'] = 'autocomplete';
            $s['body']['size'] = 1;
            $s['body']['query']['filtered']['query']['query_string']['query'] = $RusName;
            $s['body']['query']['filtered']['query']['query_string']['fields'] = array('name', 'type');
            $s['body']['highlight']['fields']['name'] = array("fragment_size" => 100);
            $s['body']['sort']['type']['order'] = 'desc';
            $s['body']['sort']['name']['order'] = 'asc';
            $s['body']['query']['filtered']['filter']['term']['type'] = 'product';

            $results = $client->search($s);
            $totalRus = $results['hits']['total'];

            $s['body']['query']['filtered']['query']['query_string']['query'] = $EngName;
            $results = $client->search($s);
            $totalEng = $results['hits']['total'];

            # autocomplete
            $type = $elasticaIndex->getType('autocomplete');
            if ($totalRus == 0) {
                $document = new \Elastica\Document(null, array('name' => $RusName, 'type' => 'product'));
                $type->addDocument($document);
            }
            if ($totalEng == 0) {
                $document = new \Elastica\Document(null, array('name' => $EngName, 'type' => 'product'));
                $type->addDocument($document);
            }
            $type->getIndex()->refresh();

            # autocomplete_ext
            $type = $elasticaIndex->getType('autocomplete_ext');
            if ($totalRus == 0) {
                $document = new \Elastica\Document(null, array('name' => $RusName, 'type' => 'product'));
                $type->addDocument($document);
            }
            if ($totalEng == 0) {
                $document = new \Elastica\Document(null, array('name' => $EngName, 'type' => 'product'));
                $type->addDocument($document);
            }
            $type->getIndex()->refresh();

            # product
            $type = $elasticaIndex->getType('autocomplete_product');
            if ($totalRus == 0) {
                $name = $RusName . ' ' . $product->getProductID();
                $document = new \Elastica\Document(null, array('name' => $name));
                $type->addDocument($document);
            }
            $type->getIndex()->refresh();
        }
        catch (\Exception $e) {
        }

        return true;
    }

    private function strip($string)
    {
        $pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
        $rep = array('', '', '&');

        return preg_replace($pat, $rep, $string);
    }

    private function checkDuplicateDocument(LifecycleEventArgs $args)
    {
        $document = $args->getEntity();
        $DocumentID = $document->getDocumentID();
        $em = $args->getEntityManager();
        $documentInDb = $em->getRepository('VidalDrugBundle:Document')->findOneByDocumentID($DocumentID);

        $pdo = $em->getConnection();
        $stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
        $stmt->execute();

        # если документ с таким идентификатором уже есть - его надо удалить, не проверяя внешних ключей
        if ($documentInDb) {
            $stmt = $pdo->prepare("DELETE FROM document WHERE DocumentID = $DocumentID");
            $stmt->execute();
        }

        # надо почистить старые связи документа
        $tables = explode(' ', 'document_indicnozology document_clphpointers documentoc_atc document_infopage art_document article_document molecule_document pharm_article_document publication_document');
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE DocumentID = {$DocumentID}");
            $stmt->execute();
        }
    }

    private function checkDuplicateProduct(LifecycleEventArgs $args)
    {
        $product = $args->getEntity();
        $ProductID = $product->getProductID();
        $em = $args->getEntityManager();
        $productInDb = $em->getRepository('VidalDrugBundle:Product')->findByProductID($ProductID);

        $pdo = $em->getConnection();
        $stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
        $stmt->execute();

        # если продукт с таким идентификатором уже есть - его надо удалить, не проверяя внешних ключей
        if ($productInDb) {
            $stmt = $pdo->prepare("DELETE FROM product WHERE ProductID = $ProductID");
            $stmt->execute();
        }

        # надо почистить старые связи документа
        $tables = explode(' ', 'product_atc product_clphgroups product_company product_document product_moleculename product_phthgrp');
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("DELETE FROM {$table} WHERE ProductID = {$ProductID}");
            $stmt->execute();
        }
    }

    private function checkDuplicateTitle(LifecycleEventArgs $args, $table)
    {
        $session = $this->container->get('session');
        $title = $args->getEntity()->getTitle();

        if ($session->has('title') && $session->get('title') == $title) {
            $pdo = $args->getEntityManager()->getConnection();
            $stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM $table WHERE title = '$title'");
            $stmt->execute();
        }

        $session->set('title', $title);
    }

    private function updateCountProducts(LifecycleEventArgs $args)
    {
        ini_set('memory_limit', -1);

        $em = $args->getEntityManager();

        $repo = $em->getRepository('VidalDrugBundle:Product');
        $companies = $em->getRepository('VidalDrugBundle:Company')->findAll();

        # ставим сколько всего у них препаратов
        for ($i = 0; $i < count($companies); $i++) {
            $count = $repo->countByCompanyID($companies[$i]->getCompanyID());
            $companies[$i]->setCountProducts($count);
        }

        $em->flush();

        $infoPages = $em->getRepository('VidalDrugBundle:InfoPage')->findAll();

        # ставим сколько всего у них препаратов
        foreach ($infoPages as $infoPage) {
            $documentIds = $em->getRepository('VidalDrugBundle:Document')->findIdsByInfoPageID($infoPage->getInfoPageID());
            $count = $em->getRepository('VidalDrugBundle:Product')->countByDocumentIDs($documentIds);
            $infoPage->setCountProducts($count);
        }

        $em->flush();
    }

    private function updateProductUrl(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $pdo = $em->getConnection();
        /** @var Product $product */
        $product = $args->getEntity();

        $url = $product->getUrl();

        if (!empty($url)) {
            $productByUrl = $em->getRepository('VidalDrugBundle:Product')->findByUrlWithoutProduct($url, $product->getId());

            if (null == $productByUrl) {
                $product->setUrl($url);
                $em->flush($product);
            }
            else {
                $numbers = explode(' ', '1 2 3 4 5 6 7 8 9 10 11 12 13 14 15');
                foreach ($numbers as $number) {
                    $tryUrl = $url . '-' . $number;
                    if (null == $em->getRepository('VidalDrugBundle:Product')->findByUrl($tryUrl)) {
                        $product->setUrl($tryUrl);
                        $em->flush($product);
                        break;
                    }
                }
            }
        }
    }

    private function updateProductNames(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $pdo = $em->getConnection();

        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName,'<SUP>','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'</SUP>','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'<SUB>','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'</SUB>','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'<BR/>','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'<BR />','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'&reg;','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'&amp;','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'&trade;','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'&alpha;','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'&beta;','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'&plusmn;','')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'  ',' ')")->execute();
        $pdo->prepare("UPDATE product SET RusName2 = REPLACE(RusName2,'\"','')")->execute();
    }

    private function transformUrl($s)
    {
        $s = str_replace('<SUP>', ' ', $s);
        $s = str_replace('</SUP>', '', $s);
        $s = str_replace('<SUB>', ' ', $s);
        $s = str_replace('</SUB>', '', $s);
        $s = str_replace('<BR/>', ' ', $s);
        $s = str_replace('<BR />', ' ', $s);
        $s = str_replace('<B>', ' ', $s);
        $s = str_replace('</B>', '', $s);
        $s = str_replace('&reg;', '', $s);
        $s = str_replace('&amp;', '', $s);
        $s = str_replace('&trade;', '', $s);
        $s = str_replace('&alpha;', '', $s);
        $s = str_replace('&beta;', '', $s);
        $s = str_replace('&plusmn;', '', $s);
        $s = str_replace('С', 'c', $s);
        $s = str_replace('с', 'c', $s);
        $s = str_replace('М', 'm', $s);
        $s = str_replace('м', 'm', $s);
        $s = str_replace('Т', 't', $s);
        $s = str_replace('т', 't', $s);
        $s = str_replace('Е', 'e', $s);
        $s = str_replace('е', 'e', $s);
        $s = str_replace('Н', 'h', $s);
        $s = str_replace('н', 'h', $s);
        $s = str_replace('В', 'b', $s);
        $s = str_replace('в', 'b', $s);
        $s = str_replace('К', 'k', $s);
        $s = str_replace('к', 'k', $s);
        $s = str_replace('Р', 'p', $s);
        $s = str_replace('Р', 'p', $s);
        $s = str_replace('А', 'a', $s);
        $s = str_replace('а', 'a', $s);
        $s = str_replace('О', 'o', $s);
        $s = str_replace('о', 'o', $s);
        $s = str_replace('(', ' ', $s);
        $s = str_replace(')', ' ', $s);
        $s = str_replace('+', ' ', $s);
        $s = str_replace('№', ' ', $s);
        $s = str_replace('"', '', $s);
        $s = str_replace("'", '', $s);
        $s = str_replace('%', '', $s);
        $s = str_replace('.', ' ', $s);
        $s = str_replace(',', ' ', $s);
        $s = str_replace('/', ' ', $s);
        $s = str_replace(' - ', ' ', $s);
        $s = str_replace('_', ' ', $s);
        $s = str_replace('  ', ' ', $s);

        $s = str_replace(' ', '-', $s);
        $s = str_replace('--', '-', $s);

        $s = strtolower($s);
        $s = trim($s, '-');
        $s = preg_replace('/[^\da-z-]/i', '', $s);

        return $s;
    }
}