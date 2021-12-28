<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass=UrlRepository::class)
 */
class Url
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $shortUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $longUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domain;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="urls")
     */
    private $userproperty;

    /**
     * @ORM\OneToMany(targetEntity=UrlStatistic::class, mappedBy="url", orphanRemoval=true)
     */
    private $statistics;

    public function __construct()
    {
        $this->statistics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getShortUrl(): ?string
    {
        return $this->shortUrl;
    }

    public function setShortUrl(string $shortUrl): self
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    public function getLongUrl(): ?string
    {
        return $this->longUrl;
    }

    public function setLongUrl(string $longUrl): self
    {
        $this->longUrl = $longUrl;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserproperty(): ?User
    {
        return $this->userproperty;
    }

    public function setUserproperty(?User $userproperty): self
    {
        $this->userproperty = $userproperty;

        return $this;
    }

    /**
     * @return Collection|UrlStatistic[]
     */
    public function getStatistics(): Collection
    {
        return $this->statistics;
    }

    public function addStatistic(UrlStatistic $statistic): self
    {
        if (!$this->statistics->contains($statistic)) {
            $this->statistics[] = $statistic;
            $statistic->setUrl($this);
        }

        return $this;
    }

    public function removeStatistic(UrlStatistic $statistic): self
    {
        if ($this->statistics->removeElement($statistic)) {
            // set the owning side to null (unless already changed)
            if ($statistic->getUrl() === $this) {
                $statistic->setUrl(null);
            }
        }

        return $this;
    }

    public function getAllClick():int
    {
        $click = 0;
        foreach ($this->statistics as $statistic){
            $click+=$statistic->getClicks();
        }
        return $click;
    }
}
