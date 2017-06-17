<?php

namespace Vidal\MainBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Vidal\MainBundle\Entity\City;

class CityToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object to a string (id).
     *
     * @param  City|null $city
     * @return string
     */
    public function transform($city)
    {
        if (null === $city) {
            return '';
        }

        return $city->getId();
    }

    /**
     * Transforms a string (id) to an object (city).
     *
     * @param  string $id
     *
     * @return City|null
     *
     * @throws TransformationFailedException if object (city) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $city = $this->om->getRepository('VidalMainBundle:City')->findOneById($id);

        if (null === $city) {
			throw new TransformationFailedException(sprintf('Город с идентификатором "%s" не найден!', $id));
        }

        return $city;
    }
}