<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Iphp\FileStoreBundle\Mapping\Annotation as FileStore;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="BannerRepository")
 * @ORM\Table(name="banner")
 * @Filestore\Uploadable
 */
class Banner extends BaseEntity
{
	/**
	 * @ORM\Column(type="array", nullable=true)
	 * @Filestore\UploadableField(mapping="banner")
	 */
	protected $banner;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Filestore\UploadableField(mapping="banner")
     */
    protected $mobileBanner;

    /**
     * @ORM\Column(length=255)
     * @Assert\NotBlank(message="Укажите название баннера (для Google Analitics)")
     */
    protected $title;

	/**
	 * @ORM\Column(length=500)
	 * @Assert\NotBlank(message="Укажите ссылку для баннера")
	 */
	protected $link;

	/**
	 * @ORM\Column(length=500, nullable=true)
	 * @Assert\Url(message="Ссылка для баннера указана некорректно")
	 */
	protected $loggedLink;

	/**
	 * @ORM\Column(type="bigint")
	 */
	protected $displayed;

	/**
	 * @ORM\Column(type="integer")
	 */
	protected $clicks;

	/** @ORM\ManyToOne(targetEntity="BannerGroup", inversedBy="banners") */
	protected $group;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $width;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $height;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $mobileWidth;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $mobileHeight;

	/** @ORM\Column(type="integer", nullable=true) */
	protected $position = 0;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $mobilePosition = 0;

    /** @ORM\Column(type="boolean") */
    protected $mobileProduct = false;

    /** @ORM\Column(type="boolean") */
    protected $mobile = true;

	/** @ORM\Column(type="boolean") */
	protected $testMode = false;

    /** @ORM\Column(type="boolean") */
    protected $indexPage = false;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $clickEvent;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $showEvent;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $displayTo;

    /**
     * @ORM\Column(length=255, nullable=true)
     */
    protected $alt;

    /** @ORM\Column(type="text", nullable=true) */
    protected $forPage;

    /** @ORM\Column(type="text", nullable=true) */
    protected $notForPage;

    public function __construct()
	{
		$this->clicks    = 0;
		$this->displayed = 0;
	}

	public function __toString()
	{
		if (!empty($this->link)) {
			return '[' . $this->id . '] ' . $this->link;
		}
		elseif ($this->id) {
			return '[' . $this->id . ']';
		}
		else {
			return '';
		}
	}

	/**
	 * @param mixed $banner
	 */
	public function setBanner($banner)
	{
		$this->banner = $banner;
	}

	/**
	 * @return mixed
	 */
	public function getBanner()
	{
		return $this->banner;
	}

	/**
	 * @param mixed $clicks
	 */
	public function setClicks($clicks)
	{
		$this->clicks = $clicks;
	}

	/**
	 * @return mixed
	 */
	public function getClicks()
	{
		return $this->clicks;
	}

	/**
	 * @param mixed $group
	 */
	public function setGroup($group)
	{
		$this->group = $group;
	}

	/**
	 * @return mixed
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @param mixed $link
	 */
	public function setLink($link)
	{
		$this->link = $link;
	}

	/**
	 * @return mixed
	 */
	public function getLink()
	{
		return $this->link;
	}

	/**
	 * Получение пути хранения изображения баннера
	 *
	 * @return null|string
	 */
	public function getPath()
	{
		return empty($this->banner['path']) ? null : $this->banner['path'];
	}

	/**
	 * @param mixed $displayed
	 */
	public function setDisplayed($displayed)
	{
		$this->displayed = $displayed;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayed()
	{
		return $this->displayed;
	}

	public function isSwf()
	{
		$ext = pathinfo($this->banner['path'], PATHINFO_EXTENSION);

		return $ext == 'swf' || $ext == 'fla';
	}

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param mixed $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

	/**
	 * @return mixed
	 */
	public function getLoggedLink()
	{
		return $this->loggedLink;
	}

	/**
	 * @param mixed $loggedLink
	 */
	public function setLoggedLink($loggedLink)
	{
		$this->loggedLink = $loggedLink;
	}

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

	/**
	 * @return mixed
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @param mixed $position
	 */
	public function setPosition($position)
	{
		$this->position = $position;
	}

    /**
     * @return mixed
     */
    public function getMobilePosition()
    {
        return $this->mobilePosition;
    }

    /**
     * @param mixed $mobilePosition
     */
    public function setMobilePosition($mobilePosition)
    {
        $this->mobilePosition = $mobilePosition;
    }

    /**
     * @return boolean
     */
    public function isMobile()
    {
        return $this->mobile;
    }

    /**
     * @param boolean $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return mixed
     */
    public function getClickEvent()
    {
        return $this->clickEvent;
    }

    /**
     * @param mixed $clickEvent
     */
    public function setClickEvent($clickEvent)
    {
        $this->clickEvent = $clickEvent;
    }

    /**
     * @return mixed
     */
    public function getShowEvent()
    {
        return $this->showEvent;
    }

    /**
     * @param mixed $showEvent
     */
    public function setShowEvent($showEvent)
    {
        $this->showEvent = $showEvent;
    }

    /**
     * @return mixed
     */
    public function getIndexPage()
    {
        return $this->indexPage;
    }

    /**
     * @param mixed $indexPage
     */
    public function setIndexPage($indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @return mixed
     */
    public function getDisplayTo()
    {
        return $this->displayTo;
    }

    /**
     * @param mixed $displayTo
     */
    public function setDisplayTo($displayTo)
    {
        $this->displayTo = $displayTo;
    }

    /**
     * @return mixed
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return mixed
     */
    public function getMobileBanner()
    {
        return $this->mobileBanner;
    }

    /**
     * @param mixed $mobileBanner
     */
    public function setMobileBanner($mobileBanner)
    {
        $this->mobileBanner = $mobileBanner;
    }

    /**
     * @return mixed
     */
    public function getMobileWidth()
    {
        return $this->mobileWidth;
    }

    /**
     * @param mixed $mobileWidth
     */
    public function setMobileWidth($mobileWidth)
    {
        $this->mobileWidth = $mobileWidth;
    }

    /**
     * @return mixed
     */
    public function getMobileHeight()
    {
        return $this->mobileHeight;
    }

    /**
     * @param mixed $mobileHeight
     */
    public function setMobileHeight($mobileHeight)
    {
        $this->mobileHeight = $mobileHeight;
    }

    /**
     * @return mixed
     */
    public function getMobileProduct()
    {
        return $this->mobileProduct;
    }

    /**
     * @param mixed $mobileProduct
     */
    public function setMobileProduct($mobileProduct)
    {
        $this->mobileProduct = $mobileProduct;
    }

    /**
     * @return mixed
     */
    public function getForPage()
    {
        return $this->forPage;
    }

    /**
     * @param mixed $forPage
     */
    public function setForPage($forPage)
    {
        $this->forPage = $forPage;
    }

    /**
     * @return mixed
     */
    public function getNotForPage()
    {
        return $this->notForPage;
    }

    /**
     * @param mixed $notForPage
     */
    public function setNotForPage($notForPage)
    {
        $this->notForPage = $notForPage;
    }

	/**
	 * @return mixed
	 */
	public function getTestMode()
	{
		return $this->testMode;
	}

	/**
	 * @param mixed $testMode
	 */
	public function setTestMode($testMode)
	{
		$this->testMode = $testMode;
	}
}