<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository
{
    public function countClick($bannerId)
    {
        $this->_em->createQuery('
            UPDATE VidalMainBundle:Banner b
            SET
              b.clicks = b.clicks + 1
            WHERE b = :bannerId
        ')->setParameter('bannerId', $bannerId)
        ->execute();
    }

    public function findMobile($groupId = null)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b')
            ->leftJoin('b.group', 'g')
            ->andWhere('g.enabled = TRUE')
            ->andWhere('b.enabled = TRUE')
            ->andWhere('b.mobile = TRUE')
            ->orderBy('b.mobilePosition', 'ASC');

        if ($groupId == null) {
            $qb->andWhere('g.id NOT IN (1,2)');
        }
        else {
            $qb->andWhere('g.id = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $qb->getQuery()->getResult();
    }

    public function findMobileProduct()
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b')
            ->leftJoin('b.group', 'g')
            ->andWhere('g.enabled = TRUE')
            ->andWhere('b.enabled = TRUE')
            ->andWhere('b.mobile = TRUE')
            ->andWhere('b.mobileProduct = TRUE')
            ->orderBy('b.mobilePosition', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findByGroup($groupId)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b')
            ->leftJoin('b.group', 'g')
            ->andWhere('g = :groupId')
            ->andWhere('g.enabled = TRUE')
            ->andWhere('b.enabled = TRUE')
            ->setParameter('groupId', $groupId)
            ->orderBy('b.position', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findEnabledById($bannerId)
    {
        $qb = $this->createQueryBuilder('b');
        $qb->select('b')
            ->andWhere('b.enabled = TRUE')
            ->andWhere('b.id = :bannerId')
            ->setParameter('bannerId', $bannerId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function countShow(Banner $banner)
    {
        $this->_em->createQuery('
            UPDATE VidalMainBundle:Banner b
            SET b.displayed = b.displayed + 1
            WHERE b = :bannerId
        ')->setParameter('bannerId', $banner->getId())
        ->execute();
    }
}