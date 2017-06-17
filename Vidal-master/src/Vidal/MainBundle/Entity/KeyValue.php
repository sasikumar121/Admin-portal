<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity(repositoryClass="KeyValueRepository") @ORM\Table(name="key_value") */
class KeyValue
{
    const API_KEY = 'api-key';
    const API_KEY_PART = 'api-key-part';
    const API_KEY_NEURO = 'api-key-neuro';
    const API_KEY_ENDOCRINO = 'api-key-endocrino';

	/** @ORM\Column(type="integer") @ORM\Id @ORM\GeneratedValue */
    protected $id;

	/** @ORM\Column(type="string", length=255) */
	protected $k;

	/** @ORM\Column(type="string", length=255) */
	protected $v;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $key
	 */
	public function setKey($key)
	{
		$this->k = $key;
	}

	/**
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->k;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->v = $value;
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->v;
	}

    /**
     * @return mixed
     */
    public function getK()
    {
        return $this->k;
    }

    /**
     * @param mixed $k
     */
    public function setK($k)
    {
        $this->k = $k;
    }

    /**
     * @return mixed
     */
    public function getV()
    {
        return $this->v;
    }

    /**
     * @param mixed $v
     */
    public function setV($v)
    {
        $this->v = $v;
    }
}
