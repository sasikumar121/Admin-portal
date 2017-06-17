<?php

namespace Vidal\MainBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vidal\DrugBundle\Entity\Publication;

class NewsController extends Controller
{
    const PUBLICATIONS_PER_PAGE = 22;
    const PUBLICATIONS_PER_PHARM = 5;

    /** @Route("/novosti/novosti_{id}.{ext}", defaults={"ext"="html"}) */
    public function r1($id)
    {
        return $this->redirect($this->generateUrl('publication', array('id' => $id)), 301);
    }

    /**
     * @Route("/next-publication/{id}", name="next_publication", options={"expose"=true})
     * @Template("VidalMainBundle:News:next_publication.html.twig")
     */
    public function nextPublicationAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        /** @var Publication $publication */
        $publication = $em->getRepository('VidalDrugBundle:Publication')
            ->findPrevPublication($id);

        $text = $publication->getBody();

        $html = $this->renderView('VidalMainBundle:News:next_publication.html.twig', array(
            'publication' => $publication,
            'text' => $text,
        ));

        return new JsonResponse(array(
            'html' => $html,
            'nextId' => $publication->getId(),
        ));
    }

    /**
     * @Route("/more-news/{currPage}", name="more_news", options={"expose"=true})
     * @Template("VidalMainBundle:News:more_news.html.twig")
     */
    public function moreNewsAction(Request $request, $currPage)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        /** @var Publication[] $publications */
        $publications = $em->getRepository('VidalDrugBundle:Publication')
            ->findMoreNews($currPage, self::PUBLICATIONS_PER_PAGE);

        $html = $this->renderView('VidalMainBundle:News:more_news.html.twig', array(
            'publications' => $publications
        ));

        return new JsonResponse($html);
    }

    /**
     * @Route("/novosti/{id}", name="publication")
     * @Template("VidalMainBundle:News:publication.html.twig")
     */
    public function publicationAction(Request $request, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');
        /** @var Publication $publication */
        $publication = $em->getRepository('VidalDrugBundle:Publication')->findOneById($id);

        if ((!$publication || $publication->getEnabled() === false) && !$request->query->has('test')) {
            throw $this->createNotFoundException();
        }

        $invisible = $this->get('security.context')->isGranted('ROLE_INVISIBLE');
        if ($invisible == false && $publication->getInvisible()) {
            throw $this->createNotFoundException();
        }

        $title = $this->strip($publication->getTitle());

        # перелинковка новостей и препаратов с document.ArticleID = 1
        $text = $publication->getBody();
        $lowerText = mb_strtolower($text, 'utf-8');
        $productsPriority = $em->getRepository('VidalDrugBundle:Product')->findByDocumentPriority();

        foreach ($productsPriority as $RusName => $p) {
            $pos = mb_strpos($lowerText, $RusName, null, 'utf-8');
            if ($pos !== false) {
                $url = empty($p['url'])
                    ? $this->generateUrl('product', array('EngName' => $p['Name'], 'ProductID' => $p['ProductID']))
                    : $this->generateUrl('product_url', array('EngName' => $p['url']));
                $regex = "/\\b{$RusName}(а|я|о|е|и|ы|ю|у|ом|ам|ем)?\\b/iu";
                $text = preg_replace($regex, '<a href="' . $url . '">$0</a>', $text);
            }
        }

        if ($request->get('t', null) == 't') {
            # перелинковка активных веществ
            $molecules = $em->getRepository('VidalDrugBundle:Molecule')->findGrouped();
            foreach ($molecules as $name => $m) {
                if (strpos($lowerText, $name) !== false) {
                    $url = $this->generateUrl('molecule', array('MoleculeID' => $m['MoleculeID']));
                    $upperName = mb_strtoupper($name, 'utf-8');
                    $replacement = "<a href=\"{$url}\" target=\"_blank\">{$upperName}</a>";
                    $text = preg_replace("/\\b{$name}\\b/ui", $replacement, $text);
                }
            }

            # перелинковка Company
            $names = $em->getRepository('VidalDrugBundle:Company')->findGrouped();
            foreach ($names as $name => $n) {
                if (strpos($lowerText, $name) !== false) {
                    $url = $this->generateUrl('firm_item', array('CompanyID' => $n['CompanyID']));
                    $upperName = mb_strtoupper($name, 'utf-8');
                    $replacement = "<a href=\"{$url}\" target=\"_blank\">{$upperName}</a>";
                    $text = preg_replace("/\\b{$name}\\b/ui", $replacement, $text);
                }
            }

            # перелинковка InfoPage
            $names = $em->getRepository('VidalDrugBundle:InfoPage')->findGrouped();
            foreach ($names as $name => $n) {
                if (strpos($lowerText, $name) !== false) {
                    $url = $this->generateUrl('inf_item', array('InfoPageID' => $n['InfoPageID']));
                    $upperName = mb_strtoupper($name, 'utf-8');
                    $replacement = "<a href=\"{$url}\" target=\"_blank\">{$upperName}</a>";
                    $text = preg_replace("/\\b{$name}\\b/ui", $replacement, $text);
                }
            }
        }

        $description = $this->stripDescr($publication->getBody(), 185);

        return array(
            'publication' => $publication,
            'text' => $text,
            'menu_left' => 'news',
            'keywords' => '',
            'seotitle' => $title . ' - Новости Видаль - cправочник лекарственных препаратов',
            'ogTitle' => $title,
            'nextPublication' => $em->getRepository('VidalDrugBundle:Publication')->findNextPublication($id),
            'prevPublication' => $em->getRepository('VidalDrugBundle:Publication')->findPrevPublication($id),
            'description' => $description,
        );
    }

    /**
     * Дополнительные случайные новости
     * @Route("/news/random/{id}", name="news_random", requirements={"id":"\d+"}, options={"expose":true})
     */
    public function newsRandomAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('drug');

        $params = array(
            'randomPublications' => $em->getRepository('VidalDrugBundle:Publication')->findRandomPublications($id)
        );

        $html = $this->renderView("VidalMainBundle:News:news_random.html.twig", $params);

        return new JsonResponse($html);
    }

    /**
     * @Route("/novosti", name="news", options={"expose"=true})
     * @Template("VidalMainBundle:News:news.html.twig")
     */
    public function newsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager('drug');
        $page = $request->query->get('p', null);
        $testMode = $request->query->has('test');
        $invisible = $this->get('security.context')->isGranted('ROLE_INVISIBLE');

        if ($page == 1) {
            return $this->redirect($this->generateUrl('news'), 301);
        }
        elseif ($page == null) {
            $page = 1;
        }

        if ($page == 1) {
            $params = array(
                'menu_left' => 'news',
                'title' => 'Новости медицины и фармации – страница 1',
            );
        }
        else {
            $params = array(
                'menu_left' => 'news',
                'title' => 'Новости медицины и фармации – страница ' . $page,
            );
        }

        if ($page == 1) {
            $params['publicationsPriority'] = $em->getRepository('VidalDrugBundle:Publication')->findLastPriority($testMode);
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository('VidalDrugBundle:Publication')->getQueryEnabled($testMode, $invisible),
            $page,
            self::PUBLICATIONS_PER_PAGE
        );
        $pagination->setTemplate('VidalMainBundle:News:news_pagination.html.twig');

        $params['publicationsPagination'] = $pagination;
        $params['keywords'] = '';
        $params['page'] = $page;

        return $params;
    }

    public function leftAction()
    {
        $em = $this->getDoctrine()->getManager('drug');

        return $this->render('VidalMainBundle:News:left.html.twig', array(
            'publications' => $em->getRepository('VidalDrugBundle:Publication')->findLeft(),
        ));
    }

    private function strip($string)
    {
        $string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
        $string = preg_replace('/&nbsp;|®|™/', '', $string);

        return $string;
    }

    private function stripDescr($string, $maxLength = null)
    {
        $string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
        $string = preg_replace('/(®|™)/iu', '', $string);
        $string = str_replace('&nbsp;', ' ', $string);
        $string = trim(preg_replace('/\s+/', ' ', $string));
        $string = mb_substr($string, 0, 175, 'utf-8');

        return $string;
    }
}
