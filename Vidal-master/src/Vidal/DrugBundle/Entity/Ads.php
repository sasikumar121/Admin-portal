<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;

/** @ORM\Entity(repositoryClass="AdsRepository") @ORM\Table(name="ads") @FileStore\Uploadable */
class Ads extends BaseEntity
{
	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @FileStore\UploadableField(mapping="video")
	 * @Assert\File(
	 *        maxSize="100M",
	 *        maxSizeMessage="Видео не может быть больше 100Мб"
	 * )
	 */
	protected $video;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $videoWidth;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $videoHeight;

    /** @ORM\Column(type="boolean") */
    protected $videoForUsersOnly = false;

    /** @ORM\Column(length=10) */
    protected $type = 'image';

    /** @ORM\Column(type="text", nullable=true) */
    protected $raw;

    /** @ORM\Column(type="text", nullable=true) */
    protected $swiffy;

    /** @ORM\Column(type="string", length=500, nullable=true) */
    protected $href;

    /**
     * @ORM\ManyToMany(targetEntity="Product", inversedBy="ads")
     * @ORM\OrderBy({"RusName2" = "ASC"})
     * @ORM\JoinTable(name="ads_product",
     *        joinColumns={@ORM\JoinColumn(name="ads_id", referencedColumnName="id")},
     *        inverseJoinColumns={@ORM\JoinColumn(name="ProductID", referencedColumnName="ProductID")})
     */
    protected $products;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @FileStore\UploadableField(mapping="avs")
     * @Assert\Image(
     *    maxSize="4M",
     *    maxSizeMessage="Принимаются фотографии размером до 4 Мб"
     * )
     */
    protected $photo;

    /** @ORM\Column(type="boolean") */
    protected $photoForUsersOnly = false;

    /** @ORM\Column(type="text", nullable=true) */
    protected $photoStyles;

    /**
     * @ORM\OneToMany(targetEntity="AdsSlider", mappedBy="ads", cascade={"persist"})
     * @ORM\OrderBy({"slideNumber" = "ASC", "priority" = "ASC"})
     */
    protected $sliders;

	public function __construct()
	{
        $this->products = new ArrayCollection();
        $this->sliders = new ArrayCollection();

		$now           = new \DateTime('now');
		$this->created = $now;
		$this->updated = $now;
		$this->date    = $now;
	}

	public function __toString()
	{
		return $this->type;
	}

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function addProduct(Product $product)
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }
    }

    public function removeProduct(Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * @return mixed
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param mixed $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return mixed
     */
    public function getVideoWidth()
    {
        return $this->videoWidth;
    }

    /**
     * @param mixed $videoWidth
     */
    public function setVideoWidth($videoWidth)
    {
        $this->videoWidth = $videoWidth;
    }

    /**
     * @return mixed
     */
    public function getVideoHeight()
    {
        return $this->videoHeight;
    }

    /**
     * @param mixed $videoHeight
     */
    public function setVideoHeight($videoHeight)
    {
        $this->videoHeight = $videoHeight;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param mixed $href
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return mixed
     */
    public function getSwiffy()
    {
        return $this->swiffy;
    }

    /**
     * @param mixed $swiffy
     */
    public function setSwiffy($swiffy)
    {
        $this->swiffy = $swiffy;
    }

    /**
     * @return mixed
     */
    public function getPhotoStyles()
    {
        return $this->photoStyles;
    }

    /**
     * @param mixed $photoStyles
     */
    public function setPhotoStyles($photoStyles)
    {
        $this->photoStyles = $photoStyles;
    }

    /**
     * @return mixed
     */
    public function getSliders()
    {
        return $this->sliders;
    }

    /**
     * @param mixed $sliders
     */
    public function setSliders($sliders)
    {
        $this->sliders = $sliders;
    }

    public function addSlider(AdsSlider $entity)
    {
        if (!$this->sliders->contains($entity)) {
            $entity->setAds($this);
            $this->sliders[] = $entity;
        }

        return $this;
    }

    public function removeSlider(AdsSlider $entity)
    {
        $this->sliders->removeElement($entity);
    }

    /**
     * @return mixed
     */
    public function getVideoForUsersOnly()
    {
        return $this->videoForUsersOnly;
    }

    /**
     * @param mixed $videoForUsersOnly
     */
    public function setVideoForUsersOnly($videoForUsersOnly)
    {
        $this->videoForUsersOnly = $videoForUsersOnly;
    }

    /**
     * @return mixed
     */
    public function getPhotoForUsersOnly()
    {
        return $this->photoForUsersOnly;
    }

    /**
     * @param mixed $photoForUsersOnly
     */
    public function setPhotoForUsersOnly($photoForUsersOnly)
    {
        $this->photoForUsersOnly = $photoForUsersOnly;
    }
}