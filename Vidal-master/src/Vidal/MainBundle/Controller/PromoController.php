<?php
namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PromoController extends Controller
{
    /**
     * @Route("/promo/glyad_linz", name="promo_linz")
     * @Template("VidalMainBundle:Promo:linz.html.twig")
     */
    public function linzAction()
    {
        throw $this->createNotFoundException();
    }

    /**
     * @Route("/rabotnikam-pervogo-stola", name="promo_neirontin")
     */
    public function neirontinAction()
    {
        $params = array(
            'title' => 'Нейронтин',
            'menu_left' => 'promo_neirontin',
        );

        return $this->render('VidalMainBundle:Promo:neirontin.html.twig', $params);
    }

    /**
     * @Route("/vidalbox", name="vidalbox")
     * @Template("VidalMainBundle:Promo:vidalbox.html.twig")
     */
    public function vidalboxAction()
    {
        $em      = $this->getDoctrine()->getManager('drug');
        $products = $em->getRepository('VidalDrugBundle:Ads')->findEnabledProducts();

        $params = array(
            'menu_left' => 'vidalbox',
            'title' => 'VIDAL BOX PREMIUM',
            'products' => $products,
        );

        return $params;
    }
}