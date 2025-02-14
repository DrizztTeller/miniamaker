<?php

namespace App\Entity;

use App\Repository\LpContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LpContentRepository::class)]
#[ORM\HasLifecycleCallbacks] // Gestion des created_at et updated_at en automatique
class LpContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'lpContent', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?LandingPage $landing_page = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $excerpt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable;
    }

    #[ORM\PrePersist]
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTimeImmutable;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLandingPage(): ?LandingPage
    {
        return $this->landing_page;
    }

    public function setLandingPage(LandingPage $landing_page): static
    {
        $this->landing_page = $landing_page;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(string $excerpt): static
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
