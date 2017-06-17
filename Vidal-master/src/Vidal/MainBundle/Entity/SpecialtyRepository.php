<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SpecialtyRepository extends EntityRepository
{
	public function findByName($name)
	{
		return $this->_em->createQuery('
			SELECT s
			FROM VidalMainBundle:Specialty s
			WHERE s.title LIKE :name
			ORDER BY s.title ASC
		')->setParameter('name', $name)
			->setMaxResults(1)
			->getOneOrNullResult();
	}
}