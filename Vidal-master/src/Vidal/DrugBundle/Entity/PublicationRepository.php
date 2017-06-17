<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PublicationRepository extends EntityRepository
{
    public function findMoreNews($currPage, $limit = 10)
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE AND p.priority IS NULL
			ORDER BY p.date DESC
		')->setFirstResult($currPage * $limit)
            ->setMaxResults($limit)
            ->getResult();
    }

    public function findRandomPublications($id, $qty = 5)
    {
        $all = $this->_em->createQuery('
			SELECT p.id
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE AND p.id != :id
		')->setParameter('id', $id)
            ->getResult();

        $allIds = array();

        foreach ($all as $p) {
            $allIds[] = $p['id'];
        }

        shuffle($allIds);

        $ids = array_splice($allIds, 0, $qty);

        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE AND p.id IN (:ids)
		')->setParameter('ids', $ids)
            ->getResult();
    }

    public function findNextPublication($id)
    {
        $next = $this->findNext($id);

        return $next ? $next : $this->findFirstOfAll();
    }

    public function findPrevPublication($id)
    {
        $prev = $this->findPrev($id);

        return $prev ? $prev : $this->findLastOfAll();
    }

    private function findFirstOfAll()
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
			ORDER BY p.id ASC
		')->setMaxResults(1)
            ->getOneOrNullResult();
    }

    private function findLastOfAll()
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
			ORDER BY p.id DESC
		')->setMaxResults(1)
            ->getOneOrNullResult();
    }

    private function findPrev($id)
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.id < :id
			ORDER BY p.id DESC
		')->setParameter('id', $id)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    private function findNext($id)
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.id > :id
			ORDER BY p.id ASC
		')->setParameter('id', $id)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function findLast($top = 4, $testMode = false, $invisible = false)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('p')
            ->from('VidalDrugBundle:Publication', 'p')
            ->andWhere('p.date < :now')
            ->andWhere('p.priority IS NULL')
            ->orderBy('p.date', 'DESC')
            ->setParameter('now', new \DateTime())
            ->setMaxResults($top);

        $testMode
            ? $qb->andWhere('p.enabled = TRUE OR p.testMode = TRUE')
            : $qb->andWhere('p.enabled = TRUE');

        if ($invisible == false) {
            $qb->andWhere('p.invisible = FALSE');
        }

        return $qb->getQuery()->getResult();
    }

    public function findLastPriority($top = 3, $testMode = false)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('p')
            ->from('VidalDrugBundle:Publication', 'p')
            ->andWhere('p.date < :now')
            ->andWhere('p.priority IS NOT NULL')
            ->orderBy('p.priority', 'DESC')
            ->addOrderBy('p.date', 'DESC')
            ->setParameter('now', new \DateTime())
            ->setMaxResults($top);

        $testMode
            ? $qb->andWhere('p.enabled = TRUE OR p.testMode = TRUE')
            : $qb->andWhere('p.enabled = TRUE');

        return $qb->getQuery()->getResult();
    }

    public function findFrom($from, $max)
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
			ORDER BY p.priority DESC, p.date DESC
		')->setParameter('now', new \DateTime())
            ->setFirstResult($from)
            ->setMaxResults($max)
            ->getResult();
    }

    public function getQueryEnabled($testMode = false, $invisible = false)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('p')
            ->from('VidalDrugBundle:Publication', 'p')
            ->andWhere('p.date < :now')
            ->andWhere('p.priority IS NULL')
            ->addOrderBy('p.date', 'DESC')
            ->setParameter('now', new \DateTime());

        $testMode
            ? $qb->andWhere('p.enabled = TRUE OR p.testMode = TRUE')
            : $qb->andWhere('p.enabled = TRUE');

        if ($invisible == false) {
            $qb->andWhere('p.invisible = FALSE');
        }

        return $qb->getQuery();
    }

    public function getQueryByTag($tagId)
    {
        return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			JOIN p.tags t
			WHERE p.enabled = TRUE
				AND p.date < :now
				AND t = :tagId
			ORDER BY p.priority DESC, p.date DESC
		')->setParameter('now', new \DateTime())
            ->setParameter('tagId', $tagId);
    }

    public function findByTagWord($tagId, $text)
    {
        if (empty($text)) {
            $results = array();

            $results1 = $this->_em->createQuery('
				SELECT p
				FROM VidalDrugBundle:publication p
				JOIN p.tags t WITH t = :tagId
			')->setParameter('tagId', $tagId)
                ->getResult();

            $results2 = $this->_em->createQuery('
				SELECT p
				FROM VidalDrugBundle:publication p
				JOIN p.infoPages i
				JOIN i.tag t WITH t = :tagId
			')->setParameter('tagId', $tagId)
                ->getResult();

            foreach ($results1 as $r) {
                $key = $r->getId();
                $results[$key] = $r;
            }
            foreach ($results2 as $r) {
                $key = $r->getId();
                if (!isset($results[$key])) {
                    $results[$key] = $r;
                }
            }

            return array_values($results);
        }
        else {
            $tagHistory = $this->_em->getRepository('VidalDrugBundle:TagHistory')->findOneByTagText($tagId, $text);
            $ids = $tagHistory->getPublicationIds();

            if (empty($ids)) {
                return array();
            }

            return $this->_em->createQuery('
				SELECT p
				FROM VidalDrugBundle:Publication p
				WHERE p.id IN (:ids)
			')->setParameter('ids', $ids)
                ->getResult();
        }
    }

    public function findByNozology($nozologyCodes)
    {
        return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Publication a
			JOIN a.nozologies n WITH n.NozologyCode IN (:codes)
			WHERE a.enabled = TRUE
			ORDER BY a.date DESC
		')->setParameter('codes', $nozologyCodes)
            ->getResult();
    }

    public function findLeft($max = 5)
    {
        $sticked = $this->_em->createQuery('
			SELECT p.id, p.title, p.date, p.announce, p.sticked
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.sticked = TRUE
			ORDER BY p.date DESC
		')->getResult();

        $fresh = $this->_em->createQuery('
			SELECT p.id, p.title, p.date, p.announce, p.sticked
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
				AND p.sticked = FALSE
			ORDER BY p.date DESC
		')->setParameter('now', new \DateTime())
            ->setMaxResults($max)
            ->getResult();

        return array_merge($sticked, $fresh);
    }

    public function findForApi($from, $size)
    {
        $publications = $this->_em->createQuery('
			SELECT p.title, p.announce, p.date, p.id
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.mobile = TRUE
			ORDER BY p.priority DESC, p.date DESC
		')->setMaxResults($size)
            ->setFirstResult($from)
            ->getResult();

        for ($i = 0; $i < count($publications); $i++) {
            $publications[$i]['date'] = $publications[$i]['date']->format('Y-m-d H:i:s');
        }

        return $publications;
    }

    public function findForApiById($id)
    {
        $publication = $this->_em->createQuery('
			SELECT p.title, p.announce, p.body, p.date, p.id
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.mobile = TRUE
				AND p.id = :id
		')->setParameter('id', $id)
            ->getOneOrNullResult();

        if (empty($publication)) {
            return array();
        }

        $publication['date'] = $publication['date']->format('Y-m-d H:i:s');

        return $publication;
    }

    public function findRawForApi($from, $size)
    {
        $publications = $this->_em->createQuery('
			SELECT p.title, p.announce, p.date, p.id
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.mobile = TRUE
			ORDER BY p.priority DESC, p.date DESC
		')->setMaxResults($size)
            ->setFirstResult($from)
            ->getResult();

        for ($i = 0; $i < count($publications); $i++) {
            $publications[$i]['date'] = $publications[$i]['date']->format('Y-m-d H:i:s');
            $publications[$i]['title'] = strip_tags($publications[$i]['title']);
            $publications[$i]['announce'] = strip_tags($publications[$i]['announce']);
        }

        return $publications;
    }

    public function findRawForApiById($id)
    {
        $publication = $this->_em->createQuery('
			SELECT p.title, p.announce, p.body, p.date, p.id
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.mobile = TRUE
				AND p.id = :id
		')->setParameter('id', $id)
            ->getOneOrNullResult();

        if (empty($publication)) {
            return array();
        }

        $publication['date'] = $publication['date']->format('Y-m-d H:i:s');
        $publication['title'] = strip_tags($publication['title']);
        $publication['announce'] = strip_tags($publication['announce']);
        $publication['body'] = strip_tags($publication['body']);

        return $publication;
    }
}