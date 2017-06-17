<?php

namespace Vidal\MainBundle\Controller;

use Doctrine\ORM\EntityManager;
use Proxies\__CG__\Vidal\MainBundle\Entity\BannerGroup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vidal\MainBundle\Entity\Banner;
use Vidal\MainBundle\Geo\Geo;

class BannerController extends Controller
{
	public function renderMobileGroupAction(Request $request, $groupId = null, $indexPage = false, $productPage = false, $vetPage = false)
	{
		/** @var EntityManager $em */
		$em = $this->getDoctrine();
		/** @var Banner[] $banners */
		$banners = $em->getRepository('VidalMainBundle:Banner')->findMobile($groupId);

		if (empty($banners)) {
			return new Response();
		}

		$pathInfo = str_replace('/app_dev.php', '', $request->getRequestUri());
		$routeName = $request->get('_route');
        $style = null;

        if ($groupId == BannerGroup::TOP) {
            $style = 'margin-bottom:28px;';
        }
        elseif ($groupId == BannerGroup::BOTTOM) {
            $style = 'margin-top:0;';
        }

		return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'request' => $request,
			'banners'  => $banners,
			'indexPage' => $indexPage,
			'productPage' => $productPage,
			'pathInfo' => $pathInfo,
			'routeName' => $routeName,
			'vetPage' => $vetPage,
            'style' => $style,
		));
	}

    public function renderMobileAction(Request $request, $indexPage = false, $productPage = false, $vetPage = false)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        /** @var Banner[] $banners */
        $banners = $em->getRepository('VidalMainBundle:Banner')->findMobile();

        if (empty($banners)) {
            return new Response();
        }

        $pathInfo = str_replace('/app_dev.php', '', $request->getRequestUri());
        $routeName = $request->get('_route');

        return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'request' => $request,
            'banners'  => $banners,
            'indexPage' => $indexPage,
            'productPage' => $productPage,
            'pathInfo' => $pathInfo,
            'routeName' => $routeName,
            'vetPage' => $vetPage,
        ));
    }

    public function renderMobileProductAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        /** @var Banner[] $banners */
        $banners = $em->getRepository('VidalMainBundle:Banner')->findMobileProduct();

        if (empty($banners)) {
            return new Response();
        }

        $pathInfo = str_replace('/app_dev.php', '', $request->getRequestUri());
        $routeName = $request->get('_route');

        return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'request' => $request,
            'banners'  => $banners,
            'indexPage' => false,
            'productPage' => true,
            'mustShow' => true,
            'pathInfo' => $pathInfo,
            'routeName' => $routeName,
            'style' => 'margin-top:20px',
        ));
    }

    /**
     * Рендеринг баннеров асинхронно
     * @Route("/banner-render-ajax/{groupId}/{indexPage}/{vetPage}/{nofollow}", name="banner_render_ajax", requirements={"id":"\d+"}, options={"expose":true})
     */
    public function bannerRenderAjaxAction(Request $request, $groupId, $indexPage = false, $vetPage = false, $nofollow = false)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        /** @var Banner[] $banners */
        $banners = $em->getRepository('VidalMainBundle:Banner')->findByGroup($groupId);

        if (empty($banners)) {
            return new JsonResponse();
        }

        $pathInfo = str_replace('/app_dev.php', '', $request->getRequestUri());
        $routeName = $request->get('_route');

        $html = $this->renderView('VidalMainBundle:Banner:render.html.twig', array(
			'request' => $request,
            'banners'  => $banners,
            'indexPage' => $indexPage,
            'productPage' => false,
            'pathInfo' => $pathInfo,
            'routeName' => $routeName,
            'vetPage' => $vetPage,
            'nofollow' => $nofollow,
        ));

        return new JsonResponse($html);
    }

	public function renderAction(Request $request, $groupId, $indexPage = false, $vetPage = false, $nofollow = false)
	{
		/** @var EntityManager $em */
		$em = $this->getDoctrine();
		/** @var Banner[] $banners */
		$banners = $em->getRepository('VidalMainBundle:Banner')->findByGroup($groupId);

        if (empty($banners)) {
            return new Response();
        }

        $pathInfo = str_replace('/app_dev.php', '', $request->getRequestUri());
        $routeName = $request->get('_route');

		return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'request' => $request,
			'banners'  => $banners,
			'indexPage' => $indexPage,
            'productPage' => false,
            'pathInfo' => $pathInfo,
            'routeName' => $routeName,
            'vetPage' => $vetPage,
            'nofollow' => $nofollow,
		));
	}

    public function renderSingleAction(Request $request, $bannerId)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        /** @var Banner $banner */
        $banner = $em->getRepository('VidalMainBundle:Banner')->findEnabledById($bannerId);

        if (null == $banner) {
            return new Response();
        }

        $pathInfo = str_replace('/app_dev.php', '', $request->getRequestUri());
        $routeName = $request->get('_route');

        return $this->render('VidalMainBundle:Banner:render.html.twig', array(
			'request' => $request,
            'banners'  => array($banner),
            'indexPage' => false,
            'productPage' => false,
            'pathInfo' => $pathInfo,
            'routeName' => $routeName,
        ));
    }

	/**
	 * Добавить клик по банеру
	 * @Route("/banner-clicked/{bannerId}", name="banner_clicked", options={"expose"=true})
	 */
	public function bannerClickedAction($bannerId)
	{
		$this->getDoctrine()
			->getRepository('VidalMainBundle:Banner')
			->countClick($bannerId);

		return new Response();
	}
}
