<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity(repositoryClass="Vidal\MainBundle\Entity\DeliveryLogRepository") @ORM\Table(name="delivery_log") */
class DeliveryLog extends BaseEntity
{
    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $userId;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $uniqueid;

    /** @ORM\Column(type = "boolean") */
    protected $failed = false;

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUniqueid()
    {
        return $this->uniqueid;
    }

    /**
     * @param mixed $uniqueid
     */
    public function setUniqueid($uniqueid)
    {
        $this->uniqueid = $uniqueid;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getFailed()
    {
        return $this->failed;
    }

    /**
     * @param mixed $failed
     */
    public function setFailed($failed)
    {
        $this->failed = $failed;
    }
}