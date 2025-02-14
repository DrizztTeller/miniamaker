<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    /**
     * @var Collection<int, LandingPage>
     */
    #[ORM\ManyToMany(targetEntity: LandingPage::class, mappedBy: 'tags')]
    private Collection $landingPages;

    public function __construct()
    {
        $this->landingPages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, LandingPage>
     */
    public function getLandingPages(): Collection
    {
        return $this->landingPages;
    }

    public function addLandingPage(LandingPage $landingPage): static
    {
        if (!$this->landingPages->contains($landingPage)) {
            $this->landingPages->add($landingPage);
            $landingPage->addTag($this);
        }

        return $this;
    }

    public function removeLandingPage(LandingPage $landingPage): static
    {
        if ($this->landingPages->removeElement($landingPage)) {
            $landingPage->removeTag($this);
        }

        return $this;
    }
}
