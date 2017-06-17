<?php

namespace Vidal\DrugBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    const BATCH_SIZE = 50;

    /**
     * @Route("/api/batch/item/{from}", name="api_batch_item")
     */
    public function batchNumberAction($from)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        $products = $em->getRepository('VidalDrugBundle:Product')->findBatchItem($from, self::BATCH_SIZE);

        $response = new Response(json_encode($products, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/api/batch/list", name="api_batch_list")
     */
    public function batchListAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        $products = $em->getRepository('VidalDrugBundle:Product')->findBatchList();

        $maxBatches = ceil(count($products) / self::BATCH_SIZE);
        $urls = array();

        for ($i = 1; $i <= $maxBatches; $i++) {
            $urls[] = 'https://www.vidal.ru/api/batch/item/' . $i;
        }

        $response = new Response(json_encode($urls, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/api/drug/gui", name="api_drug_gui")
     * @Template("VidalDrugBundle:Api:gui.html.twig")
     */
    public function guiAction()
    {
        return array(
            'title' => 'Поиск аналогов препаратов'
        );
    }

    /** @Route("/api/drug/autocomplete-product/{term}/{type}", name="api_drug_autocomplete_product", options={"expose":true}) */
    public function autocompleteProductAction($term, $type)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        $products = $em->getRepository('VidalDrugBundle:Product')->findByTerm($term);

        return new JsonResponse($products);
    }

    /**
     * @Route("/api/drug/equal/{ProductID}/{EqRateType}", name="api_drug_equal")
     */
    public function equalAction($ProductID, $EqRateType = null)
    {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Vidal"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Для доступа необходимо ввести логин и пароль';
            exit;
        }
        elseif ($_SERVER['PHP_AUTH_USER'] !== 'apidrug' && $_SERVER['PHP_AUTH_PW'] !== 'equal') {
            header('WWW-Authenticate: Basic realm="Vidal"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Не верный логин или пароль!';
            exit;
        }

        $ProductTypeCode = 'DRUG';
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        $pdo = $em->getConnection();
        $product = $em->createQuery("
            SELECT p
            FROM VidalDrugBundle:Product p
            WHERE (p.ProductTypeCode = :ProductTypeCode)
              AND p.ProductID = :ProductID
        ")->setParameter('ProductTypeCode', $ProductTypeCode)
            ->setParameter('ProductID', $ProductID)
            ->getOneOrNullResult();

        $params = array('product' => $product);
        $params['ProductID'] = $ProductID;
        $params['EqRateType'] = $EqRateType;

        if ($product == null) {
            return new JsonResponse(array('error' => "Product was not found by ProductID $ProductID"), 400);
        }

        $RoaRaw = $em->createQuery("
            SELECT iroa.RouteID
            FROM VidalDrugBundle:ProductItem pri
            JOIN VidalDrugBundle:ProductItemRoute iroa
              WITH iroa.ProductID = pri.ProductID AND iroa.ItemID = pri.ItemID
            WHERE pri.ProductID = :ProductID
        ")->setParameter('ProductID', $ProductID)
            ->getResult();
        $roa = array();

        foreach ($RoaRaw as $r) {
            $roa[] = $r['RouteID'];
        }

        $roaQ = empty($roa) ? '0' : implode(',', $roa);

        if ($EqRateType <= 0 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT mol.MoleculeID
                FROM product_moleculename pmn
                INNER JOIN moleculename mn ON mn.MoleculeNameID = pmn.MoleculeNameID
                INNER JOIN molecule mol ON mol.MoleculeID = mn.MoleculeID
                WHERE pmn.ProductID = $ProductID
                  AND mol.MoleculeID NOT IN (2203,1144)
            ");
            $stmt->execute();
            $midRaw = $stmt->fetchAll();
            $mid = array();

            foreach ($midRaw as $r) {
                $mid[] = $r['MoleculeID'];
            }

            $MCount = count($mid);
            if ($MCount == 0 && $EqRateType != 4) {
                return new JsonResponse(array('error' => "Molecules was not found by ProductID $ProductID AND EqRateType=0"), 400);
            }
            $midQ = empty($mid) ? '0' : implode(',', $mid);

            $stmt = $pdo->prepare("
                SELECT pr.ProductID
                FROM product pr
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(pmn.MoleculeNameID)
                    from product_moleculename pmn
                    inner join moleculename mn on mn.MoleculeNameID = pmn.MoleculeNameID
                    inner join molecule mol on mol.MoleculeID = mn.MoleculeID
                    where pmn.ProductID = pr.ProductID and mol.MoleculeID not in (2203,1144)
                  ) > 0
                  AND (
                    select COUNT(mol.MoleculeID)
                    from product_moleculename pmn
                    inner join moleculename mn on mn.MoleculeNameID = pmn.MoleculeNameID
                    inner join molecule mol on mol.MoleculeID = mn.MoleculeID
                    where pmn.ProductID = pr.ProductID
                      and mol.MoleculeID not in (2203,1144)
                      AND mol.MoleculeID not in ($midQ)
                   ) = 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");

            $stmt->execute();
            $raw = $stmt->fetchAll();
            $productIds = array();

            foreach ($raw as $r) {
                $productIds[] = $r['ProductID'];
            }

            $params['productIds'] = $productIds;

            if ($EqRateType == 4) {
                if (!empty($productIds)) {
                    return new JsonResponse(array('type' => 0, 'ids' => $productIds));
                }
            }
            else {
                return new JsonResponse(array('type' => $EqRateType, 'ids' => $productIds));
            }
        }

        # ATC связи
        $stmt = $pdo->prepare("
            SELECT t.ATCCode, t.Level
            FROM atc t
            WHERE t.Level IN (3,4,5) AND t.ATCCode IN (
              SELECT patc.ATCCode
              FROM product_atc patc
              INNER JOIN product pr ON pr.ProductID = patc.ProductID
              WHERE patc.ProductID = $ProductID
            )
        ");
        $stmt->execute();
        $atcRaw = $stmt->fetchAll();

        if (empty($atcRaw) && $EqRateType != 4) {
            return new JsonResponse(array('error' => "ATC codes were not found by ProductID $ProductID AND EqRateType=$EqRateType"), 400);
        }

        $atc3 = array();
        $atc4 = array();
        $atc5 = array();

        foreach ($atcRaw as $a) {
            if ($a['Level'] == 3) {
                $atc3[] = $a['ATCCode'];
            }
            elseif ($a['Level'] == 4) {
                $atc4[] = $a['ATCCode'];
            }
            elseif ($a['Level'] == 5) {
                $atc5[] = $a['ATCCode'];
            }
        }

        $atc3Q = empty($atc3) ? "'0'" : "'" . implode("','", $atc3) . "'";
        $atc4Q = empty($atc4) ? "'0'" : "'" . implode("','", $atc4) . "'";
        $atc5Q = empty($atc5) ? "'0'" : "'" . implode("','", $atc5) . "'";

        /* --- Близкие аналоги --- */
        if ($EqRateType == 1 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT pr.ProductID
                FROM product pr
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(patc.ProductID)
                    from product_atc patc
                    where patc.ProductID = pr.ProductID
                      AND patc.ATCCode IN ($atc5Q)
                  ) > 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");

            $stmt->execute();
            $raw = $stmt->fetchAll();
            $productIds = array();

            foreach ($raw as $r) {
                $productIds[] = $r['ProductID'];
            }

            if ($EqRateType == 4) {
                if (!empty($productIds)) {
                    return new JsonResponse(array('type' => 1, 'ids' => $productIds));
                }
            }
            else {
                return new JsonResponse(array('type' => $EqRateType, 'ids' => $productIds));
            }
        }

        /* --- Приблизительные аналоги --- */
        if ($EqRateType == 2 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT pr.ProductID
                FROM product pr
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(patc.ProductID)
                    from product_atc patc
                    where patc.ProductID = pr.ProductID
                      AND patc.ATCCode IN ($atc4Q)
                  ) > 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");
            $stmt->execute();
            $raw = $stmt->fetchAll();
            $productIds = array();

            foreach ($raw as $r) {
                $productIds[] = $r['ProductID'];
            }

            if ($EqRateType == 4) {
                if (!empty($productIds)) {
                    return new JsonResponse(array('type' => 2, 'ids' => $productIds));
                }
            }
            else {
                return new JsonResponse(array('type' => $EqRateType, 'ids' => $productIds));
            }
        }

        /* --- Препараты с близкими свойствами --- */
        if ($EqRateType == 3 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT pr.ProductID
                FROM product pr
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(patc.ProductID)
                    from product_atc patc
                    where patc.ProductID = pr.ProductID
                      AND patc.ATCCode IN ($atc3Q)
                  ) > 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");
            $stmt->execute();
            $raw = $stmt->fetchAll();
            $productIds = array();

            foreach ($raw as $r) {
                $productIds[] = $r['ProductID'];
            }

            if ($EqRateType == 4) {
                if (!empty($productIds)) {
                    return new JsonResponse(array('type' => 3, 'ids' => $productIds));
                }
            }
            else {
                return new JsonResponse(array('type' => $EqRateType, 'ids' => $productIds));
            }
        }
    }

    /**
     * @Route("/api/drug/equal-ajax/{ProductID}/{EqRateType}", name="api_drug_equal_ajax", options={"expose":true})
     */
    public function equalAjaxAction($ProductID, $EqRateType = null)
    {
        $ProductTypeCode = 'DRUG';
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        $pdo = $em->getConnection();
        $product = $em->createQuery("
            SELECT p
            FROM VidalDrugBundle:Product p
            WHERE (p.ProductTypeCode = :ProductTypeCode)
              AND p.ProductID = :ProductID
        ")->setParameter('ProductTypeCode', $ProductTypeCode)
            ->setParameter('ProductID', $ProductID)
            ->getOneOrNullResult();

        $params = array('product' => $product);
        $params['ProductID'] = $ProductID;
        $params['EqRateType'] = $EqRateType;
        $anyProducts = array();

        if ($product == null) {
            return new JsonResponse(array('error' => "Product was not found by ProductID $ProductID"), 400);
        }

        $RoaRaw = $em->createQuery("
            SELECT iroa.RouteID
            FROM VidalDrugBundle:ProductItem pri
            JOIN VidalDrugBundle:ProductItemRoute iroa
              WITH iroa.ProductID = pri.ProductID AND iroa.ItemID = pri.ItemID
            WHERE pri.ProductID = :ProductID
        ")->setParameter('ProductID', $ProductID)
            ->getResult();
        $roa = array();

        foreach ($RoaRaw as $r) {
            $roa[] = $r['RouteID'];
        }

        $roaQ = empty($roa) ? '0' : implode(',', $roa);

        # полные аналоги препарата
        if ($EqRateType <= 0 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT mol.MoleculeID
                FROM product_moleculename pmn
                INNER JOIN moleculename mn ON mn.MoleculeNameID = pmn.MoleculeNameID
                INNER JOIN molecule mol ON mol.MoleculeID = mn.MoleculeID
                WHERE pmn.ProductID = $ProductID
                  AND mol.MoleculeID NOT IN (2203,1144)
            ");
            $stmt->execute();
            $midRaw = $stmt->fetchAll();
            $mid = array();

            foreach ($midRaw as $r) {
                $mid[] = $r['MoleculeID'];
            }

            $MCount = count($mid);
            if ($MCount == 0 && $EqRateType != 4) {
                return new JsonResponse(array('error' => "Molecules was not found by ProductID $ProductID AND EqRateType=0"), 400);
            }
            $midQ = empty($mid) ? '0' : implode(',', $mid);

            $stmt = $pdo->prepare("
                SELECT pr.ProductID, pr.RusName2, pr.Name, pr.url, pr.ZipInfo, pr.forms, 
                  co.GDDBName, cn.RusName countryName
                FROM product pr
                LEFT JOIN product_company pc ON pc.ProductID = pr.ProductID AND pc.ItsMainCompany = 1
                LEFT JOIN company co ON co.CompanyID = pc.CompanyID
                LEFT JOIN country cn ON cn.CountryCode = co.CountryCode
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(pmn.MoleculeNameID)
                    from product_moleculename pmn
                    inner join moleculename mn on mn.MoleculeNameID = pmn.MoleculeNameID
                    inner join molecule mol on mol.MoleculeID = mn.MoleculeID
                    where pmn.ProductID = pr.ProductID and mol.MoleculeID not in (2203,1144)
                  ) > 0
                  AND (
                    select COUNT(mol.MoleculeID)
                    from product_moleculename pmn
                    inner join moleculename mn on mn.MoleculeNameID = pmn.MoleculeNameID
                    inner join molecule mol on mol.MoleculeID = mn.MoleculeID
                    where pmn.ProductID = pr.ProductID
                      and mol.MoleculeID not in (2203,1144)
                      AND mol.MoleculeID not in ($midQ)
                   ) = 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");

            $stmt->execute();
            $products = $stmt->fetchAll();

            if ($EqRateType == 4) {
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $key = $product['ProductID'];
                        $anyProducts[$key] = $product;
                    }
                }
            }
            else {
                return new JsonResponse($products);
            }
        }

        # ATC связи
        $stmt = $pdo->prepare("
            SELECT t.ATCCode, t.Level
            FROM atc t
            WHERE t.Level IN (3,4,5) AND t.ATCCode IN (
              SELECT patc.ATCCode
              FROM product_atc patc
              INNER JOIN product pr ON pr.ProductID = patc.ProductID
              WHERE patc.ProductID = $ProductID
            )
        ");
        $stmt->execute();
        $atcRaw = $stmt->fetchAll();

        if (empty($atcRaw) && $EqRateType != 4) {
            return new JsonResponse();
        }

        $atc3 = array();
        $atc4 = array();
        $atc5 = array();

        foreach ($atcRaw as $a) {
            if ($a['Level'] == 3) {
                $atc3[] = $a['ATCCode'];
            }
            elseif ($a['Level'] == 4) {
                $atc4[] = $a['ATCCode'];
            }
            elseif ($a['Level'] == 5) {
                $atc5[] = $a['ATCCode'];
            }
        }

        $atc3Q = empty($atc3) ? "'0'" : "'" . implode("','", $atc3) . "'";
        $atc4Q = empty($atc4) ? "'0'" : "'" . implode("','", $atc4) . "'";
        $atc5Q = empty($atc5) ? "'0'" : "'" . implode("','", $atc5) . "'";

        /* --- Близкие аналоги --- */
        if ($EqRateType == 1 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT pr.ProductID, pr.RusName2, pr.Name, pr.url, pr.ZipInfo, pr.forms, 
                  co.GDDBName, cn.RusName countryName
                FROM product pr
                LEFT JOIN product_company pc ON pc.ProductID = pr.ProductID AND pc.ItsMainCompany = 1
                LEFT JOIN company co ON co.CompanyID = pc.CompanyID
                LEFT JOIN country cn ON cn.CountryCode = co.CountryCode
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(patc.ProductID)
                    from product_atc patc
                    where patc.ProductID = pr.ProductID
                      AND patc.ATCCode IN ($atc5Q)
                  ) > 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");

            $stmt->execute();
            $products = $stmt->fetchAll();

            if ($EqRateType == 4) {
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $key = $product['ProductID'];
                        $anyProducts[$key] = $product;
                    }
                }
            }
            else {
                return new JsonResponse($products);
            }
        }

        /* --- Приблизительные аналоги --- */
        if ($EqRateType == 2 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT pr.ProductID, pr.RusName2, pr.Name, pr.url, pr.ZipInfo, pr.forms, 
                  co.GDDBName, cn.RusName countryName
                FROM product pr
                LEFT JOIN product_company pc ON pc.ProductID = pr.ProductID AND pc.ItsMainCompany = 1
                LEFT JOIN company co ON co.CompanyID = pc.CompanyID
                LEFT JOIN country cn ON cn.CountryCode = co.CountryCode
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(patc.ProductID)
                    from product_atc patc
                    where patc.ProductID = pr.ProductID
                      AND patc.ATCCode IN ($atc4Q)
                  ) > 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");

            $stmt->execute();
            $products = $stmt->fetchAll();

            if ($EqRateType == 4) {
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $key = $product['ProductID'];
                        $anyProducts[$key] = $product;
                    }
                }
            }
            else {
                return new JsonResponse($products);
            }
        }

        /* --- Препараты с близкими свойствами --- */
        if ($EqRateType == 3 || $EqRateType == 4) {
            $stmt = $pdo->prepare("
                SELECT pr.ProductID, pr.RusName2, pr.Name, pr.url, pr.ZipInfo, pr.forms, 
                  co.GDDBName, cn.RusName countryName
                FROM product pr
                LEFT JOIN product_company pc ON pc.ProductID = pr.ProductID AND pc.ItsMainCompany = 1
                LEFT JOIN company co ON co.CompanyID = pc.CompanyID
                LEFT JOIN country cn ON cn.CountryCode = co.CountryCode
                WHERE pr.ProductID != $ProductID
                  AND pr.ProductTypeCode = '$ProductTypeCode'
                  AND (
                    select COUNT(patc.ProductID)
                    from product_atc patc
                    where patc.ProductID = pr.ProductID
                      AND patc.ATCCode IN ($atc3Q)
                  ) > 0
                  AND (
                    select COUNT(*)
                    from product_item pri
                    inner join product_item_route iroa ON iroa.ProductID = pri.ProductID and iroa.ItemID = pri.ItemID
                    where pri.ProductID = pr.ProductID and iroa.RouteID in ($roaQ)
                  ) > 0
                  AND pr.MarketStatusID IN (1,2,7)
                  AND pr.ProductTypeCode NOT IN ('SUBS')
                  AND pr.IsNotForSite = 0
                  AND pr.parent_id IS NULL
                  AND pr.MainID IS NULL
            ");

            $products = $stmt->fetchAll();

            if ($EqRateType == 4) {
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $key = $product['ProductID'];
                        $anyProducts[$key] = $product;
                    }
                }
            }
            else {
                return new JsonResponse($products);
            }
        }

        return new JsonResponse(array_values($anyProducts));
    }
}
