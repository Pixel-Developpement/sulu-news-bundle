<?php

namespace Pixel\NewsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\CategoryBundle\Entity\Category;
use Sulu\Bundle\CategoryBundle\Entity\CategoryInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="Pixel\NewsBundle\Repository\NewsRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class News
{
    public const RESOURCE_KEY = "news";
    public const FORM_KEY = "news_details";
    public const LIST_KEY = "news";
    public const SECURITY_CONTEXT = "news.news";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryInterface::class)
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Expose()
     */
    private Category $category;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\Expose()
     */
    private ?MediaInterface $cover;

    /**
     * @var Collection<string, NewsTranslation>
     * @ORM\OneToMany(targetEntity="Pixel\NewsBundle\Entity\NewsTranslation", mappedBy="news", cascade={"ALL"}, indexBy="locale")
     * @Serializer\Exclude
     */
    private $translations;


    /**
     * @var string
     * * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private string $locale = 'fr';

    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     * @Serializer\VirtualProperty(name="title")
     */
    public function getTitle(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getTitle();
    }

    protected function getTranslation(string $locale = 'fr'): ?NewsTranslation
    {
        if (!$this->translations->containsKey($locale)) {
            return null;
        }
        return $this->translations->get($locale);
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setTitle($title);
        return $this;
    }

    protected function createTranslation(string $locale): NewsTranslation
    {
        $translation = new NewsTranslation($this, $locale);
        $this->translations->set($locale, $translation);
        return $translation;
    }

    /**
     * @Serializer\VirtualProperty(name="route")
     * @return string|null
     */
    public function getRoutePath(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getRoutePath();
    }

    /**
     * @param string $routePath
     * @return self
     */
    public function setRoutePath(string $routePath): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setRoutePath($routePath);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="is_published")
     * @return bool|null
     */
    public function isPublished(): ?bool
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->isPublished();
    }

    /**
     * @param bool|null $isPublished
     * @return self
     */
    public function setIsPublished(?bool $isPublished): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setIsPublished($isPublished);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="seo")
     * @return array|null
     */
    public function getSeo(): ?array
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getSeo();
    }

    /**
     * @Serializer\VirtualProperty(name="ext")
     * @return array|null
     */
    public function getExt(): ?array
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return ($translation->getSeo()) ? ['seo' => $translation->getSeo()] : ['seo' => ["title" => "", "description" => ""]];
    }

    /**
     * @param array|null $seo
     * @return self
     */
    public function setSeo(?array $seo): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setSeo($seo);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="content")
     * @return array|null
     */
    public function getContent(): ?array
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }
        return $translation->getContent();
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(CategoryInterface $category): void
    {
        $this->category = $category;
    }

    /**
     * @return MediaInterface|null
     */
    public function getCover(): ?MediaInterface
    {
        return $this->cover;
    }

    /**
     * @param MediaInterface|null $cover
     */
    public function setCover(?MediaInterface $cover): void
    {
        $this->cover = $cover;
    }

    /**
     * @param array $content
     * @return self
     */
    public function setContent(array $content): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setContent($content);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="published_at")
     * @return \DateTimeImmutable|null
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        $translation = $this->getTranslation($this->locale);
        if(!$translation){
            return null;
        }
        return $translation->getPublishedAt();
    }

    public function setPublishedAt(?\DateTimeImmutable $date): self
    {
        $translation = $this->getTranslation($this->locale);
        if(!$translation){
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setPublishedAt($date);
        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="excerpt")
     * @return array|null
     */
    public function getExcerpt(): ?array
    {
        $translation = $this->getTranslation($this->locale);
        if(!$translation){
            return null;
        }
        return $translation->getExcerpt();
    }

    public function setExcerpt(?array $excerpt): self
    {
        $translation = $this->getTranslation($this->locale);
        if(!$translation){
            $translation = $this->createTranslation($this->locale);
        }
        $translation->setExcerpt($excerpt);
        return $this;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultLocale(): ?string
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $defaultLocale
     */
    public function setDefaultLocale(string $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }



}
