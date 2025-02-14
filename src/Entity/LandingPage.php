<?php

namespace App\Entity;

use App\Repository\LandingPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LandingPageRepository::class)]
#[ORM\HasLifecycleCallbacks] // Gestion des created_at et updated_at en automatique
class LandingPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'landingPages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Detail $detail = null;

    #[ORM\Column(length: 80)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToOne(mappedBy: 'landing_page', cascade: ['persist', 'remove'])]
    private ?LpContent $lpContent = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'landingPages')]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }
    
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

    public function getDetail(): ?Detail
    {
        return $this->detail;
    }

    public function setDetail(?Detail $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getLpContent(): ?LpContent
    {
        return $this->lpContent;
    }

    public function setLpContent(LpContent $lpContent): static
    {
        // set the owning side of the relation if necessary
        if ($lpContent->getLandingPage() !== $this) {
            $lpContent->setLandingPage($this);
        }

        $this->lpContent = $lpContent;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
