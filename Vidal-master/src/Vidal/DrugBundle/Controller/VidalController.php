<?php

namespace Vidal\DrugBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vidal\DrugBundle\Entity\Document;
use Vidal\DrugBundle\Entity\InfoPage;
use Vidal\DrugBundle\Entity\Molecule;
use Vidal\DrugBundle\Entity\Product;
use Vidal\DrugBundle\Entity\RiglaRegion;
use Vidal\MainBundle\Geo\IPGeoBase;

class VidalController extends Controller
{
    const PRODUCTS_PER_PAGE = 40;
    const COMPANIES_PER_PAGE = 50;
    const MOLECULES_PER_PAGE = 50;

    /** @Route("/poisk_preparatov") */
    public function r1()
    {
        return $this->redirect($this->generateUrl('drugs'), 301);
    }

    /** @Route("/BAD/opisanie/{url}") */
    public function r4($url = null)
    {
        return $this->redirect($this->generateUrl('drugs'), 301);
    }

    /** @Route("/patsientam/spisok-boleznei-po-alfavitu/") */
    public function r5()
    {
        return $this->redirect($this->generateUrl('disease'), 301);
    }

    /** @Route("/poisk_preparatov/fir_{url}", requirements={"url"=".+"}) */
    public function redirectFirm($url)
    {
        if ($pos = strrpos($url, '.')) {
            $url = substr($url, 0, $pos);
        }

        $CompanyID = $url;
        $em = $this->getDoctrine()->getManager('drug');
        $company = $em->getRepository('VidalDrugBundle:Company')->findByCompanyID($CompanyID);

        if ($company == null) {
            throw $this->createNotFoundException();
        }

        $products = $em->getRepository('VidalDrugBundle:Product')->findByOwner($CompanyID);

        if (empty($products)) {
            $firstLetter = mb_substr($company['CompanyName'], 0, 1);
            return $this->redirect($this->generateUrl('companies', array('l' => $firstLetter)), 301);
        }

        return $this->redirect($this->generateUrl('firm_item', array('CompanyID' => $url)), 301);
    }

    /** @Route("/poisk_preparatov/lfir_{url}", requirements={"url"=".+"}) */
    public function redirectLfirm($url)
    {
        if ($pos = strrpos($url, '.')) {
            $url = substr($url, 0, $pos);
        }

        $CompanyID = $url;
        $em = $this->getDoctrine()->getManager('drug');
        $company = $em->getRepository('VidalDrugBundle:Company')->findByCompanyID($CompanyID);

        if ($company == null) {
            throw $this->createNotFoundException();
        }

        $products = $em->getRepository('VidalDrugBundle:Product')->findByOwner($CompanyID);

        if (empty($products)) {
            $firstLetter = mb_substr($company['CompanyName'], 0, 1);
            return $this->redirect($this->generateUrl('companies', array('l' => $firstLetter)), 301);
        }

        return $this->redirect($this->generateUrl('firm_item', array('CompanyID' => $url)), 301);
    }

    /**
     * Список препаратов по компании
     *
     * @Route("/drugs/firm/{CompanyID}", name="firm_item", requirements={"CompanyID":"\d+"})
     * @Template("VidalDrugBundle:Vidal:firm_item.html.twig")
     */
    public function firmItemAction($CompanyID)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $company = $em->getRepository('VidalDrugBundle:Company')->findByCompanyID($CompanyID);

        if ($company == null) {
            throw $this->createNotFoundException();
        }

        $products = $em->getRepository('VidalDrugBundle:Product')->findByOwner($CompanyID);

        if (empty($products)) {
            $firstLetter = mb_substr($company['CompanyName'], 0, 1);

            return $this->redirect($this->generateUrl('companies', array('l' => $firstLetter)), 301);
        }

        # находим представительства
        $productsRepresented = array();
        for ($i = 0; $i < count($products); $i++) {
            $key = $products[$i]['InfoPageID'];
            if (!empty($key) && !isset($productsRepresented[$key])) {
                $productsRepresented[$key] = $products[$i];
            }
        }

        $params = array(
            'title' => $this->strip($company['CompanyName']) . ' | Фирмы-производители',
            'company' => $company,
            'productsRepresented' => $productsRepresented,
            'products' => $products,
        );

        if (!empty($products)) {
            $productIds = $this->getProductIds($products);
            $params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
            $params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
        }

        return $params;
    }

    /**
     * Список препаратов по клиннико-фармакологической группе
     *
     * @Route("/drugs/cl-ph-group/{description}", name="clphgroup")
     * @Template("VidalDrugBundle:Vidal:clphgroup.html.twig")
     */
    public function clphgroupAction($description)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $products = $em->getRepository('VidalDrugBundle:Product')->findByClPhGroup($description);
        $params = array(
            'products' => $products,
            'description' => $description,
            'title' => 'Клинико-фармакологическая группа',
        );

        if (!empty($products)) {
            $productIds = $this->getProductIds($products);
            $params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
            $params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
        }

        return $params;
    }

    /** @Route("/poisk_preparatov/inf_{url}", requirements={"url"=".+"}) */
    public function redirectInfopage($url)
    {
        if ($pos = strrpos($url, '.')) {
            $url = substr($url, 0, $pos);
        }

        return $this->redirect($this->generateUrl('inf_item', array('InfoPageID' => $url)), 301);
    }

    /** @Route("/poisk_preparatov/linf_{url}", requirements={"url"=".+"}) */
    public function redirectLInfopage($url)
    {
        if ($pos = strrpos($url, '.')) {
            $url = substr($url, 0, $pos);
        }

        return $this->redirect($this->generateUrl('inf_item', array('InfoPageID' => $url)), 301);
    }

    /**
     * Страничка представительства и список препаратов
     *
     * @Route("/drugs/company/{InfoPageID}", name="inf_item", requirements={"InfoPageID":"\d+"})
     * @Template("VidalDrugBundle:Vidal:inf_item.html.twig")
     */
    public function infItemAction($InfoPageID)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        /** @var InfoPage $infoPage */
        $infoPage = $em->getRepository('VidalDrugBundle:InfoPage')->findOneByInfoPageID($InfoPageID);

        if (!$infoPage || $infoPage->getCountProducts() == 0) {
            throw $this->createNotFoundException();
        }

        $picture = $em->getRepository('VidalDrugBundle:Picture')->findByInfoPageID($InfoPageID);
        $documentIds = $em->getRepository('VidalDrugBundle:Document')->findIdsByInfoPageID($InfoPageID);
        $params = array(
            'infoPage' => $infoPage,
            'picture' => $picture,
            'title' => $this->strip($infoPage->getRusName()) . ' | Представительства фирм',
            'portfolios' => $em->getRepository('VidalDrugBundle:InfoPage')->findPortfolios($InfoPageID),
        );

        if (!empty($documentIds)) {
            $products = $em->getRepository('VidalDrugBundle:Product')->findByDocumentIDs($documentIds);

            if (!empty($products)) {
                $productsBads = array();
                $productsLp = array();
                $productsMi = array();

                foreach ($products as $product) {
                    if ($product['ArticleID'] == 6) {
                        $productsBads[] = $product;
                    }
                    elseif ($product['ArticleID'] == 8 || $product['ArticleID'] == 7) {
                        $productsMi[] = $product;
                    }
                    else {
                        $productsLp[] = $product;
                    }
                }

                $params['productsBads'] = $productsBads;
                $params['productsLp'] = $productsLp;
                $params['productsMi'] = $productsMi;

                $productIds = $this->getProductIds($products);
                $params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
                $params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
            }
        }

        return $params;
    }

    /**
     * @Route("/drugs/molecules", name="molecules")
     * @Template("VidalDrugBundle:Vidal:molecules.html.twig")
     */
    public function moleculesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $q = $request->query->get('q', null);
        $l = $request->query->get('l', null);
        $p = $request->query->get('p', 1);

        if ($l) {
            $query = $em->getRepository('VidalDrugBundle:Molecule')->getQueryByLetter($l);
        }
        elseif ($q) {
            $query = $em->getRepository('VidalDrugBundle:Molecule')->getQueryByString($q);
        }
        else {
            $query = $em->getRepository('VidalDrugBundle:Molecule')->getQuery();
        }

        $params = array(
            'menu_drugs' => 'molecule',
            'title' => 'Активные вещества',
            'q' => $q,
            'l' => $l,
            'pagination' => $this->get('knp_paginator')->paginate($query, $p, self::MOLECULES_PER_PAGE),
        );

        return $params;
    }

    /** @Route("/poisk_preparatov/act_{url}", requirements={"url"=".+"}) */
    public function redirectMolecule($url)
    {
        if ($pos = strrpos($url, '.')) {
            $url = substr($url, 0, $pos);
        }

        return $this->redirect($this->generateUrl('molecule', array('MoleculeID' => $url)), 301);
    }

    /**
     * Список препаратов по активному веществу: одно-монокомпонентные
     * @Route("/drugs/molecule/{MoleculeID}/{search}", name="molecule", requirements={"MoleculeID":"\d+"})
     * @Template("VidalDrugBundle:Vidal:molecule.html.twig")
     */
    public function moleculeAction($MoleculeID, $search = 0)
    {
        $em = $this->getDoctrine()->getManager('drug');
        /** @var Molecule $molecule */
        $molecule = $em->getRepository('VidalDrugBundle:Molecule')->findByMoleculeID($MoleculeID);

        if (!$molecule) {
            throw $this->createNotFoundException();
        }

        /** @var Document $document */
        $document = $em->getRepository('VidalDrugBundle:Document')->findByMoleculeID($MoleculeID);
        $params = array(
            'molecule' => $molecule,
            'document' => $document,
            'title' => mb_strtoupper($molecule->getTitle(), 'utf-8') . ' | Активные вещества',
        );

        $description = $this->mb_ucfirst($this->strip($molecule->getLatName()))
            . ' (' . $this->mb_ucfirst($this->strip($molecule->getRusName())) . ')';

        if ($document) {
            $description .= ' ' . $this->truncateHtml($document->getPhInfluence(), 180);
        }

        $params['description'] = $description;

        return $search ? $this->render('VidalDrugBundle:Vidal:search_molecule.html.twig', $params) : $params;
    }

    private function truncateHtml($text, $length = 100)
    {
        return mb_substr(strip_tags($text), 0, $length, 'UTF-8');
    }

    /** @Route("/poisk_preparatov/lact_{url}", requirements={"url"=".+"}) */
    public function redirectLMolecule($url)
    {
        if ($pos = strrpos($url, '.')) {
            $url = substr($url, 0, $pos);
        }

        return $this->redirect($this->generateUrl('molecule_included', array('MoleculeID' => $url)), 301);
    }

    /**
     * Отображение списка препаратов, в состав которых входит активное вещество (Molecule)
     *
     * @Route("/drugs/molecule-in/{MoleculeID}", name="molecule_included", requirements={"MoleculeID":"\d+"})
     * @Template("VidalDrugBundle:Vidal:molecule_included.html.twig")
     */
    public function moleculeIncludedAction($MoleculeID)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $molecule = $em->getRepository('VidalDrugBundle:Molecule')->findByMoleculeID($MoleculeID);

        if (!$molecule) {
            throw $this->createNotFoundException();
        }

        # все продукты по активному веществу и отсеиваем дубли
        $productsRaw = $em->getRepository('VidalDrugBundle:Product')->findByMoleculeID($MoleculeID);

        if (empty($productsRaw)) {
            return array('molecule' => $molecule);
        }

        $products = array();
        $productIds = array();

        for ($i = 0; $i < count($productsRaw); $i++) {
            $key = $productsRaw[$i]['ProductID'];

            if (!isset($products[$key])) {
                $products[$key] = $productsRaw[$i];
                $productIds[] = $key;
            }
        }

        # препараты надо разбить на монокомнонентные и многокомпонентные группы
        $components = $em->getRepository('VidalDrugBundle:Molecule')->countComponents($productIds);
        $products1 = array();
        $products2 = array();

        foreach ($products as $id => $product) {
            $components[$id] == 1
                ? $products1[$id] = $product
                : $products2[$id] = $product;
        }

        uasort($products1, array($this, 'sortProducts'));
        uasort($products2, array($this, 'sortProducts'));

        $description = 'Инструкции лекарственных препаратов, содержащих активное вещество ';
        $description .= $this->mb_ucfirst($this->strip($molecule->getLatName()))
            . ' (' . $this->mb_ucfirst($this->strip($molecule->getRusName())) . ')';

        $description .= ' в справочнике лекарственных препаратов Видаль: наименование, форма выпуска и дополнительная информация';

        return array(
            'description' => $description,
            'molecule' => $molecule,
            'products1' => $products1,
            'products2' => $products2,
            'companies' => $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds),
            'infoPages' => $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($productsRaw),
            'title' => mb_strtoupper($molecule->getTitle(), 'utf-8') . ' | Активные вещества в препаратах',
        );
    }

    /**
     * Страничка рассшифровки МНН аббревиатур
     *
     * @Route("drugs/gnp", name="gnp")
     * @Route("poisk_preparatov/gnp.{ext}", name="gnp_old", defaults={"ext"="htm"})
     * @Template("VidalDrugBundle:Vidal:gnp.html.twig")
     */
    public function gnpAction(Request $request)
    {
        if ($request->get('_route') == 'gnp_old') {
            return $this->redirect($this->generateUrl('gnp'));
        }

        $em = $this->getDoctrine()->getManager('drug');

        $params = array(
            'title' => 'Международные наименования - МНН',
            'gnps' => $em->getRepository('VidalDrugBundle:MoleculeBase')->findAll(),
        );

        return $params;
    }

    /** @Route("/poisk_preparatov/{EngName}__{ProductID}.{ext}", requirements={"ProductID":"\d+", "EngName"=".+"}, defaults={"ext"="htm"}) */
    public function redirectProduct($EngName, $ProductID)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $product = $em->getRepository('VidalDrugBundle:Product')->findByProductID($ProductID);

        if (!$product) {
            return $this->redirect($this->generateUrl('drugs'), 301);
        }

        # SUBS redirects
        if (in_array($product->getProductTypeCode(), array('SUBS', 'SRED'))) {
            return $this->redirectSubs($product, $em);
        }

        # REDIRECT BY URL
        $url = $product->getUrl();
        if (!empty($url)) {
            return $this->redirect($this->generateUrl('product_url', array(
                'EngName' => $url,
            )), 301);
        }

        # REDIRECT BY PARENT
        /** @var Product $parentProduct */
        if ($parentProduct = $product->getParent()) {
            $url = $parentProduct->getUrl();
            $redirectUrl = empty($url)
                ? $this->generateUrl('product', array('EngName' => $parentProduct->getName(), 'ProductID' => $parentProduct->getId()))
                : $this->generateUrl('product_url', array('EngName' => $url));
            return $this->redirect($redirectUrl, 301);
        }

        return $this->redirect($this->generateUrl('product', array(
            'ProductID' => $ProductID,
            'EngName' => $product->getName(),
        )), 301);
    }

    /**
     * @Route("/drugs/product-group/{ids}", name="product-group")
     * @Template("VidalDrugBundle:Vidal:product_group.html.twig")
     */
    public function productGroupAction($ids)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $ids = explode('-', $ids);
        $products = array();
        $productIds = array();

        $params = array();

        foreach ($ids as $id) {
            $id = intval($id);
            $productIds[] = $id;
            $products[] = $em->getRepository('VidalDrugBundle:Product')->findFieldsByProductID($id);
        }

        $params['products'] = $products;
        $params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
        $params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);

        return $params;
    }

    /**
     * Статьи и материалы к препарату
     * @Route("/drugs/documents-of-product/{ProductID}", name="documents_of_product", requirements={"ProductID":"\d+"}, options={"expose":true})
     */
    public function documentsOfProductAction($ProductID)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        /** @var Product $product */
        $product = $em->getRepository('VidalDrugBundle:Product')->findByProductID($ProductID);

        if (!$product || $product->getInactive()) {
            throw $this->createNotFoundException();
        }

        $params = array('product' => $product);
        $params['publicationsByProduct'] = $em->getRepository('VidalDrugBundle:Product')->publicationsByProduct($ProductID);
        $params['publicationsByMolecule'] = $em->getRepository('VidalDrugBundle:Product')->publicationsByMolecule($ProductID);
        $params['articlesByProduct'] = $em->getRepository('VidalDrugBundle:Product')->articlesByProduct($ProductID);
        $params['articlesByMolecule'] = $em->getRepository('VidalDrugBundle:Product')->articlesByMolecule($ProductID);
        $params['artsByProduct'] = $em->getRepository('VidalDrugBundle:Product')->artsByProduct($ProductID);
        $params['artsByMolecule'] = $em->getRepository('VidalDrugBundle:Product')->artsByMolecule($ProductID);

        $atcCodes = $em->getRepository('VidalDrugBundle:Product')->findAllATC($product);
        if (count($atcCodes) > 0) {
            $params['publicationsByAtc'] = $em->getRepository('VidalDrugBundle:Product')->publicationsByAtc($atcCodes);
            $params['articlesByAtc'] = $em->getRepository('VidalDrugBundle:Product')->articlesByAtc($atcCodes);
            $params['artsByAtc'] = $em->getRepository('VidalDrugBundle:Product')->artsByAtc($atcCodes);
        }

        $html = $this->renderView('VidalDrugBundle:Vidal:documents_of_product.html.twig', $params);

        return new JsonResponse($html);
    }

    /**
     * Описание препарата
     * @Route("/drugs/{EngName}__{ProductID}", name="product", requirements={"ProductID":"\d+", "EngName"=".+"}, options={"expose":true})
     * @Route("/drugs/{EngName}", name="product_url", requirements={"EngName"=".+"}, defaults={"ProductID" = 0}, options={"expose":true})
     * @Template("VidalDrugBundle:Vidal:document.html.twig")
     */
    public function productAction($EngName, $ProductID)
    {
        if (in_array($ProductID, array(40431, 40433, 40432, 32336, 42446))) {
            return $this->redirect($this->generateUrl('product', array(
                'EngName' => 'prevenar_13',
                'ProductID' => 32333
            )), 301);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        $request = $this->getRequest();
        $isIdRoute = $request->get('_route') == 'product';

        /** @var Product $product */
        $product = $isIdRoute
            ? $em->getRepository('VidalDrugBundle:Product')->findByProductID($ProductID)
            : $em->getRepository('VidalDrugBundle:Product')->findByUrl($EngName);

        if (!$product || $product->getInactive()) {
            throw $this->createNotFoundException();
        }

        # REDIRECT BY PARENT
        /** @var Product $parentProduct */
        if ($parentProduct = $product->getParent()) {
            $url = $parentProduct->getUrl();
            $redirectUrl = empty($url)
                ? $this->generateUrl('product', array('EngName' => $parentProduct->getName(), 'ProductID' => $parentProduct->getId()))
                : $this->generateUrl('product_url', array('EngName' => $url));
            return $this->redirect($redirectUrl, 301);
        }

        # REDIRECT BY MainID
        $MainID = $product->getMainID();
        if (!empty($MainID)) {
            /** @var Product $mainProduct */
            if ($mainProduct = $em->getRepository('VidalDrugBundle:Product')->findOneByProductID($MainID)) {
                $url = $mainProduct->getUrl();
                $redirectUrl = empty($url)
                    ? $this->generateUrl('product', array('EngName' => $mainProduct->getName(), 'ProductID' => $mainProduct->getId()))
                    : $this->generateUrl('product_url', array('EngName' => $url));
                return $this->redirect($redirectUrl, 301);
            }
        }

        # REDIRECT BY URL
        $url = $product->getUrl();
        if (!empty($url) && $isIdRoute) {
            return $this->redirect($this->generateUrl('product_url', array(
                'EngName' => $url,
            )), 301);
        }

        if ($isIdRoute && $product->getName() != str_replace(' ', '_', $EngName)) {
            $url = $this->generateUrl('product', array('EngName' => $product->getName(), 'ProductID' => $ProductID));

            return $this->redirect($url, 301);
        }

        if (!in_array($product->getMarketStatusID()->getMarketStatusID(), array(1, 2, 7)) || $product->getInactive()) {
            throw $this->createNotFoundException();
        }

        $params = array();
        $document = $product->getDocument();

        # условите от Марии, что бады должны иметь Document.ArticleID = 6
        if ($product->getProductTypeCode() == 'BAD' && $document && $document->getArticleID() != 6) {
            $document = null;
        }

        if ($document) {
            $documentId = $document->getDocumentID();
            $params['document'] = $document;
            $params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByDocumentID($documentId);
            $params['nozologies'] = $em->getRepository('VidalDrugBundle:Nozology')->findByDocumentID($documentId);
            $params['parentATCCode'] = $em->getRepository('VidalDrugBundle:ATC')->getParent($product);
        }

        $productId = $product->getProductID();
        $productIds = array($productId);
        $atcCodes = $em->getRepository('VidalDrugBundle:Product')->findAllATC($product);

        $params['product'] = $product;
        $params['productPage'] = true;
        $params['isIdRoute'] = $isIdRoute;
        $params['productAtcCodes'] = $atcCodes;

        $altTitle = preg_replace('/<sup\b[^>]*>(.*?)<\/sup>/i', '', $product->getRusName());
        $altTitle = mb_strtolower($altTitle, 'utf-8') . ' инструкция по применению';
        $altTitle = $this->mb_ucfirst($altTitle);
        $params['img_alt_title'] = $altTitle;

        $params['products'] = array($product);
        $params['owners'] = $em->getRepository('VidalDrugBundle:Company')->findOwnersByProducts($productIds);
        $params['distributors'] = $em->getRepository('VidalDrugBundle:Company')->findDistributorsByProducts($productIds);
        $params['molecules'] = $em->getRepository('VidalDrugBundle:Molecule')->findByProductID($productId);

        $title = $this->strip($product->getRusName());
        $params['ogTitle'] = $title;
        $params['zip'] = $this->strip($product->getZipInfo());

        # тз Сеошника - description
        $description = $product->getDescription();
        if (!empty($description)) {
            $params['description'] = $description;
        }

        # медицинские изделия выводятся по-другому
        if ($product->isMI()) {
            $params['seotitle'] = $title
                . $this->strip($product->getEngName())
                . ' ' . $product->getZipInfo()
                . ' - справочник препаратов и лекарств';
            $params['isMI'] = true;
            $params['keywords'] = "";

            return $this->render("VidalDrugBundle:Vidal:bad_document.html.twig", $params);
        }

        # БАДы выводятся по-другому
        if ($product->isBAD() || ($document && $document->isBAD())) {
            $params['seotitle'] = $title
                . ' инструкция по применению: показания, противопоказания, побочное действие – описание '
                . $this->strip($product->getEngName())
                . ' ' . $product->getZipInfo()
                . ' - справочник препаратов и лекарств';
            $params['keywords'] = "";

            return $this->render("VidalDrugBundle:Vidal:bad_document.html.twig", $params);
        }

        $params['seotitle'] = $title
            . ' инструкция по применению: показания, противопоказания, побочное действие – описание '
            . $this->strip($product->getEngName())
            . ' ' . $product->getZipInfo()
            . ' - справочник препаратов и лекарств';
        $params['keywords'] = "";

        # SUBS redirects
        if (in_array($product->getProductTypeCode(), array('SUBS', 'SRED'))) {
            return $this->redirectSubs($product, $em);
        }

        return $params;
    }

    private function redirectSubs(Product $product, EntityManager $em)
    {
        $rusName = $product->getRusName2();
        /** @var Product $sameProduct */
        $sameProduct = $em->getRepository('VidalDrugBundle:Product')->findSame($rusName);

        if ($sameProduct == null) {
            return $this->redirect($this->generateUrl('drugs'), 301);
        }

        return $this->redirect($this->generateUrl('product', array(
            'EngName' => $sameProduct->getName(),
            'ProductID' => $sameProduct->getProductID(),
        )), 301);
    }

    /** @Route("/poisk_preparatov/{name}.htm", requirements={"name":"[^~]+"}) */
    public function moleculeRedirect($name)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $molecule = $em->getRepository('VidalDrugBundle:Molecule')->findByName($name);

        if (!$molecule) {
            return $this->redirect($this->generateUrl('drugs'), 301);
        }

        return $this->redirect($this->generateUrl('molecule', array('MoleculeID' => $molecule['MoleculeID'])), 301);
    }

    public function mb_ucfirst($str, $enc = 'utf-8')
    {
        return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc) . mb_strtolower(mb_substr($str, 1, mb_strlen($str, $enc), $enc), $enc);
    }

    /**
     * @Route("/drugs/{EngName}~{DocumentID}", requirements={"DocumentID":"\d+"})
     * @Route("/poisk_preparatov/{EngName}~{DocumentID}.{ext}", requirements={"DocumentID":"\d+"}, defaults={"ext"="htm"})
     */
    public function redirectDocument($EngName, $DocumentID = null)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $document = null;

        if ($DocumentID) {
            $document = $em->getRepository('VidalDrugBundle:Document')->findOneByDocumentID($DocumentID);
        }

        if (!$document) {
            $document = $em->getRepository('VidalDrugBundle:Document')->findOneByName($EngName);
        }

        if (!$document) {
            throw $this->createNotFoundException();
        }

        $products = $document->getProducts();

        if (empty($products)) {
            throw $this->createNotFoundException();
        }

        /** @var Product $product */
        $product = $products[0];

        # SUBS redirects
        if (in_array($product->getProductTypeCode(), array('SUBS', 'SRED'))) {
            return $this->redirectSubs($product, $em);
        }

        # REDIRECT BY URL
        $url = $product->getUrl();
        if (!empty($url)) {
            return $this->redirect($this->generateUrl('product_url', array(
                'EngName' => $url,
            )), 301);
        }

        # REDIRECT BY PARENT
        /** @var Product $parentProduct */
        if ($parentProduct = $product->getParent()) {
            $url = $parentProduct->getUrl();
            $redirectUrl = empty($url)
                ? $this->generateUrl('product', array('EngName' => $parentProduct->getName(), 'ProductID' => $parentProduct->getId()))
                : $this->generateUrl('product_url', array('EngName' => $url));
            return $this->redirect($redirectUrl, 301);
        }

        return $this->redirect($this->generateUrl('product', array(
            'EngName' => $products[0]->getName(),
            'ProductID' => $products[0]->getProductID(),
        )), 301);
    }

    public function riglaAction($riglaPrice, $product)
    {
        $params['riglaPrice'] = $riglaPrice;
        $params['product'] = $product;

        return $this->render('VidalDrugBundle:Vidal:rigla.html.twig', $params);
    }

    public function riglaBuyAction($riglaPrice, $product)
    {
        $params['riglaPrice'] = $riglaPrice;
        $params['product'] = $product;

        return $this->render('VidalDrugBundle:Vidal:rigla_buy.html.twig', $params);
    }

    /** Получить массив идентификаторов продуктов */
    private function getProductIds($products)
    {
        $productIds = array();

        foreach ($products as $product) {
            $productIds[] = isset($product['ProductID']) ? $product['ProductID'] : $product->getProductID();
        }

        return $productIds;
    }

    /** Отсортировать препараты по имени */
    private function sortProducts($a, $b)
    {
        return strcasecmp($a['RusName'], $b['RusName']);
    }

    private function strip($string)
    {
        $string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));

        return trim(str_replace(explode(' ', '® ™'), '', $string));
    }
}
