<?php

namespace Vidal\MainBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Vidal\MainBundle\Entity\User;
use Vidal\MainBundle\Entity\AstrazenecaFaq;

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
			'preUpdate',
            'preRemove',
		);
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof User) {
			if ($city = $entity->getCity()) {
				if ($region = $city->getRegion()) {
					$entity->setRegion($region);
				}
				if ($country = $city->getCountry()) {
					$entity->setCountry($country);
				}
			}
		}
	}

	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof User) {
			if ($city = $entity->getCity()) {
				if ($region = $city->getRegion()) {
					$entity->setRegion($region);
				}
				if ($country = $city->getCountry()) {
					$entity->setCountry($country);
				}
			}
		}
		elseif ($entity instanceof AstrazenecaFaq) {
			$answer = $entity->getAnswer();
			empty($answer) ? $entity->setEnabled(0) : $entity->setEnabled(1);
		}
	}

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            /** @var EntityManager $em */
            $em = $args->getEntityManager();
            $pdo = $em->getConnection();
            $pdo->prepare('SET FOREIGN_KEY_CHECKS=0')->execute();
            $pdo->prepare('DELETE FROM user_device WHERE user_id = ' . $entity->getId())->execute();
        }
    }
}