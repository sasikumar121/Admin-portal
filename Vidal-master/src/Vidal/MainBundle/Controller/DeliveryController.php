<?php

namespace Vidal\MainBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Vidal\MainBundle\Entity\Digest;
use Vidal\MainBundle\Entity\Delivery;
use Vidal\MainBundle\Entity\DeliveryOpen;
use Vidal\MainBundle\Entity\Region;
use Vidal\MainBundle\Entity\Specialty;

class DeliveryController extends Controller
{
    /**
     * Открыли письмо - записали в БД и вернули как бы картинку
     * @Route("/delivery/opened/{deliveryName}/{userId}", name="delivery_opened")
     */
    public function deliveryOpened($deliveryName, $userId)
    {
        $em       = $this->getDoctrine()->getManager();
        $delivery = $em->getRepository('VidalMainBundle:Delivery')->findOneByName($deliveryName);
        $user     = $em->getRepository('VidalMainBundle:User')->findOneById($userId);

        if (null == $delivery || null == $user) {
            throw $this->createNotFoundException();
        }

        $do = new DeliveryOpen();
        $do->setUser($user);
        $do->setDelivery($delivery);
        $em->persist($do);
        $em->flush();

        header('Content-Type: image/gif');
        echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
        die();
    }

    public function deliveriesAction()
    {
        $em         = $this->getDoctrine()->getManager();
        $deliveries = $em->getRepository('VidalMainBundle:Delivery')->findAll();

        return $this->render('VidalMainBundle:Delivery:deliveries.html.twig', array(
            'deliveries' => $deliveries,
        ));
    }

    /**
     * @Route("/delivery/add", name="delivery_add")
     * @Template("VidalMainBundle:Delivery:add.html.twig")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function addAction()
    {
        $delivery = new Delivery();

        $form = $this->createFormBuilder()
            ->add('name', 'null', array('label' => 'Код рассылки', 'requered' => true))
            ->add('subject', 'null', array('label' => 'Заголовок письма', 'requered' => true))
            ->add('template', 'null', array('label' => 'Шаблон письма', 'requered' => true))
            ->getForm();

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/delivery/preview", name="delivery_preview")
     * @Template("VidalMainBundle:Digest:preview.html.twig")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function previewAction()
    {
        return array();
    }

    /**************************************************************************************************************/

    /**************************************************************************************************************/

    /**************************************************************************************************************/

    /**
     * @Route("/delivery/reset", name="delivery_reset")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryResetAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();

        $em->createQuery('UPDATE VidalMainBundle:User u SET u.send=0 WHERE u.send=1')->execute();
        $em->createQuery('UPDATE VidalMainBundle:Digest d SET d.progress = 0')->execute();

        @unlink(__DIR__ . '/../Command/.delivery');

        $this->calculateDigest($em, $digest);

        $this->get('session')->getFlashBag()->add('msg', 'Сброшены разосланные флаги, рассылка остановлена');

        return $this->redirect($this->generateUrl('delivery_control'),301);
    }

    /**
     * @Route("/delivery/stop", name="delivery_stop")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryStopAction()
    {
        $em = $this->getDoctrine()->getManager();
        $em->createQuery('UPDATE VidalMainBundle:Digest d SET d.progress = 0')->execute();

        @unlink(__DIR__ . '/../Command/.delivery');

        $this->get('session')->getFlashBag()->add('msg', 'Рассылка остановлена');

        return $this->redirect($this->generateUrl('delivery_control'),301);
    }

    /**
     * @Route("/delivery/start", name="delivery_start")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryStartAction()
    {
        $em = $this->getDoctrine()->getManager();

        $em->createQuery('UPDATE VidalMainBundle:Digest d SET d.progress = 1')->execute();
        $this->get('session')->getFlashBag()->add('msg', 'Рассылка будет запущена в течении 5 минут');

        return $this->redirect($this->generateUrl('delivery_control'),301);
    }

    /**
     * @Route("/delivery/control", name="delivery_control")
     * @Template("VidalMainBundle:Digest:delivery_control.html.twig")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryControlAction(Request $request)
    {
        /** @var EntityManager $em */
        $em     = $this->getDoctrine()->getManager();
		/** @var Digest $digest */
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();
		$this->calculateDigest($em, $digest);

        $form = $this->createFormBuilder($digest)
            ->add('regions', null, array('label' => 'Регионы', 'required' => false))
            ->add('specialties', null, array('label' => 'Специальности', 'required' => false))
            ->add('allSpecialties', null, array('label' => 'Всем специальностям', 'required' => false))
            ->add('total', null, array('label' => 'Всего к отправке', 'required' => false, 'disabled' => true))
            ->add('totalSend', null, array('label' => 'Уже отправлено по рассылке ' . $digest->getUniqueid(), 'required' => false, 'disabled' => true))
            ->add('totalLeft', null, array('label' => 'Осталось отправить', 'required' => false, 'disabled' => true))
            ->add('limit', null, array('label' => 'Лимит писем', 'required' => false))
            ->add('uniqueid', null, array('label' => 'Текстовый идентификатор рассылки: латинские буквы, цифры, _'))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn-red')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('msg', 'Изменения сохранены');

            return $this->redirect($this->generateUrl('delivery_control'),301);
        }

        $params = array(
            'title'  => 'Рассылка - управление',
            'form'   => $form->createView(),
            'digest' => $digest,
        );

        return $params;
    }

    /**
     * @Route("/delivery/stats", name="delivery_stats")
     * @Template("VidalMainBundle:Digest:delivery_stats.html.twig")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryStatsAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $pdo = $em->getConnection();

        $stmt = $pdo->prepare("SELECT * FROM digest_opened WHERE id >= 89091 AND uniqueid NOT IN ('0a03602253933db2a5d02c8d801b3480', 'aaa007', 'aaa003', 'aaa010', 'aaa011', '7a03602253933db2a5d02c8d801b3487', 'df3933db2a5d02c8d801b3hf00', '5a03602253933bd8a5d02c8d801b348a', 'af3933db2a5d02c8d801b3hf88')");
        $stmt->execute();
        $opened = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT * FROM delivery_log WHERE uniqueid NOT IN ('aaa010', 'aaa011')");
        $stmt->execute();
        $logs = $stmt->fetchAll();

        $grouped = array();

        foreach ($logs as $log) {
            $uid = $log['uniqueid'];

            if (!isset($grouped[$uid])) {
                $grouped[$uid] = array('logs' => array(), 'opened' => array());
                $grouped[$uid]['failed'] = 0;
                $grouped[$uid]['total'] = 0;
            }

            $grouped[$uid]['total']++;
            if ($log['failed']) {
                $grouped[$uid]['failed']++;
            }
        }

        foreach ($opened as $open) {
            $uid = $open['uniqueid'];

            if (!isset($grouped[$uid])) {
                $grouped[$uid] = array('logs' => array(), 'opened' => array());
                $grouped[$uid]['failed'] = 0;
                $grouped[$uid]['total'] = 0;
            }

            $grouped[$uid]['opened'][] = $open['user'];
        }

        foreach ($grouped as $uid => &$group) {
            $group['opened_unique'] = count(array_unique($group['opened']));
            $group['opened'] = count($group['opened']);

            if ($uid == 'vidalcardio160317') {
                $group['total'] = '38843';
            }
        }

        return array(
            'title' => 'Рассылка - статистика',
            'grouped' => $grouped,
        );
    }

    /**
     * @Route("/delivery", name="delivery")
     * @Template("VidalMainBundle:Digest:delivery.html.twig")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        /** @var Digest $digest */
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();

        $form = $this->createFormBuilder($digest)
            ->add('text', null, array('label' => 'Текст письма', 'required' => true, 'attr' => array('class' => 'ckeditorfull')))
            ->add('subject', null, array('label' => 'Тема письма', 'required' => true))
            ->add('font', null, array('label' => 'Название шрифта без кавычек', 'required' => true))
            ->add('emails', null, array('label' => 'Тестовые e-mail через ;', 'required' => false))
            ->add('submit', 'submit', array('label' => 'Сохранить', 'attr' => array('class' => 'btn-red')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $digest->updateTextImages();
            $em->flush();
            $this->get('session')->getFlashBag()->add('msg', 'Изменения сохранены');

            return $this->redirect($this->generateUrl('delivery'),301);
        }

        $params = array(
            'title'        => 'Рассылка - письмо',
            'digest'       => $digest,
            'form'         => $form->createView(),
            'total'        => $em->getRepository('VidalMainBundle:User')->total(),
            'subscribed'   => $em->getRepository('VidalMainBundle:Digest')->countSubscribed(),
            'unsubscribed' => $em->getRepository('VidalMainBundle:Digest')->countUnsubscribed(),
        );

        return $params;
    }

    /**
     * @Route("/delivery/test", name="delivery_test")
     * @Secure(roles="ROLE_SUPERADMIN")
     */
    public function deliveryTestAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $digest = $em->getRepository('VidalMainBundle:Digest')->get();

        $emails = explode(';', $digest->getEmails());
        $this->testTo($emails, $digest);
        $this->get('session')->getFlashBag()->add('msg', 'Было отправлено на адреса: ' . $digest->getEmails());

        return $this->redirect($this->generateUrl('delivery'),301);
    }

    private function testTo($emails, $digest)
    {
        $service = $this->get('email.service');
        $em      = $this->getDoctrine()->getManager();

        foreach ($emails as $email) {
            $email = trim($email);
            $user  = $em->getRepository('VidalMainBundle:User')->findOneByUsername($email);
            if ($user) {
                $service->send(
                    $email,
                    array('VidalMainBundle:Email:digest.html.twig', array('digest' => $digest, 'user' => $user)),
                    $digest->getSubject()
                );
            }
			else {
				$service->send(
					$email,
					array('VidalMainBundle:Email:digest-test.html.twig', array('digest' => $digest)),
					$digest->getSubject()
				);
			}
        }
    }

    private function calculateDigest(EntityManager $em, Digest $digest)
    {
        # считаем, сколько всего к отправке
        $qb = $em->createQueryBuilder();
        $qb->select("COUNT(u.id)")
            ->from('VidalMainBundle:User', 'u')
            ->andWhere('u.digestSubscribed = 1');

        # специальности
        /** @var Specialty[] $specialties */
        $specialties = $digest->getSpecialties();

        if (count($specialties)) {
            $ids = array();
            foreach ($specialties as $specialty) {
                $ids[] = $specialty->getId();
            }
            $qb->andWhere('(u.primarySpecialty IN (:ids) OR u.secondarySpecialty IN (:ids))')
                ->setParameter('ids', $ids);
        }

        # регионы
        /** @var Region[] $regions */
        $regions = $digest->getRegions();

        if (count($regions)) {
            $regionIds = array();
            foreach ($regions as $region) {
                $regionIds[] = $region->getId();
            }
            $qb->andWhere('u.region IN (:regionIds)')
                ->setParameter('regionIds', $regionIds);
        }

        $total = $qb->getQuery()->getSingleScalarResult();
        $digest->setTotal($total);

        # считаем, сколько отправлено
        $totalSend = $em->createQuery('SELECT COUNT(DISTINCT l.email) FROM VidalMainBundle:DeliveryLog l WHERE l.uniqueid = :uniqueid')
            ->setParameter('uniqueid', $digest->getUniqueid())
            ->getSingleScalarResult();

        $digest->setTotalSend($totalSend);

        # cчитаем, сколько осталось отправить
        $left = $digest->getTotal() - $digest->getTotalSend();
        $digest->setTotalLeft($left < 0 ? 0 : $left);

        $em->flush($digest);
    }
}
