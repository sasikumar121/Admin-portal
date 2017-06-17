<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InfoPageRepository extends EntityRepository
{
	public function findByLetter($l)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, c.RusName Country, i.Name, i.photo, i.countProducts
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i.RusName LIKE :letter
			ORDER BY i.RusName ASC
		')->setParameter('letter', $l . '%')
			->getResult();
	}

    public function getNames()
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('i.RusName')
            ->from('VidalVeterinarBundle:InfoPage', 'i')
            ->orderBy('i.RusName', 'ASC')
            ->where("i.CountryEditionCode = 'RUS'");

        $results = $qb->getQuery()->getResult();
        $names   = array();

        foreach ($results as $result) {
            $name = preg_replace('/ &.+; /', ' ', $result['RusName']);
            $name = preg_replace('/&.+;/', ' ', $name);
            $name = mb_strtolower($name, 'UTF-8');

            $names[] = $name;
        }

        $uniques = array();

        foreach ($names as $name) {
            if (!isset($uniques[$name])) {
                $uniques[$name] = '';
            }
        }

        return array_keys($uniques);
    }

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('i.InfoPageID, i.RusName, country.RusName Country, i.Name, i.photo, i.countProducts')
			->from('VidalVeterinarBundle:InfoPage', 'i')
			->leftJoin('VidalVeterinarBundle:Country', 'country', 'WITH', 'country.CountryCode = i.CountryCode')
			->orderBy('i.RusName', 'ASC');

		$where = '';
		$words = explode(' ', $q);

		# поиск по всем словам вместе
		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' AND ';
			}
			$where .= "(i.RusName LIKE '$word%' OR i.RusName LIKE '% $word%')";
		}

		$qb->where($where);
        $qb->andWhere('i.countProducts > 0');
		$results = $qb->getQuery()->getResult();

		# поиск по одному слову
		if (empty($results)) {
			$where = '';
			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' OR ';
				}
				$where .= "(i.RusName LIKE '$word%' OR i.RusName LIKE '% $word%')";
			}
			$qb->where($where);
            $qb->andWhere('i.countProducts > 0');

			return $qb->getQuery()->getResult();
		}

		return $results;
	}

	public function findByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country, i.Name, i.photo, i.countProducts
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i = :InfoPageID
		')->setParameter('InfoPageID', $InfoPageID)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, c.RusName Country, i.Name, i.countProducts
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN i.documents d
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE d.DocumentID = :DocumentID
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findOneByName($name)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country, i.Name, i.photo, i.countProducts
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i.Name = :name
		')->setParameter('name', $name)
			->getOneOrNullResult();
	}

	public function findAllOrdered()
	{
		return $this->_em->createQuery('
			SELECT i.Name, i.RusName, c.RusName Country, i.photo, i.countProducts
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i.countProducts > 0
			ORDER BY i.RusName
		')->getResult();
	}

	public function findPortfolios($InfoPageID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT p
			FROM VidalVeterinarBundle:PharmPortfolio p
			JOIN p.DocumentID d
			JOIN d.infoPages i
			WHERE i.InfoPageID = :InfoPageID
			ORDER BY p.title ASC
		')->setParameter('InfoPageID', $InfoPageID)
			->getResult();
	}
}