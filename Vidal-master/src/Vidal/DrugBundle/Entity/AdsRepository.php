<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AdsRepository extends EntityRepository
{
    public function findEnabled()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('a')
            ->from('VidalDrugBundle:Ads', 'a')
            ->where('a.enabled = TRUE');

        return $qb->getQuery()->getResult();
    }

    public function findEnabledProducts()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('p')
            ->from('VidalDrugBundle:Product', 'p')
            ->join('p.ads', 'a')
            ->where('a.enabled = TRUE')
            ->orderBy('p.RusName2', 'ASC');

        return $qb->getQuery()->getResult();
    }
}