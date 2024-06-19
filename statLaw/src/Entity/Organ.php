<?php

namespace App\Entity;

use App\Repository\OrganRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: OrganRepository::class)]
#[Broadcast]
class Organ
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $uid = null;

    #[ORM\Column(length: 255)]
    private ?string $codeType = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private ?string $shortLabel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'organs')]
    private ?self $parentOrgan = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentOrgan')]
    private Collection $organs;

    /**
     * @var Collection<int, Actor>
     */
    #[ORM\ManyToMany(targetEntity: Actor::class, mappedBy: 'organs')]
    private Collection $actors;

    public function __construct()
    {
        $this->organs = new ArrayCollection();
        $this->actors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }

    public function setUid(string $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getCodeType(): ?string
    {
        return $this->codeType;
    }

    public function setCodeType(string $codeType): static
    {
        $this->codeType = $codeType;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getShortLabel(): ?string
    {
        return $this->shortLabel;
    }

    public function setShortLabel(string $shortLabel): static
    {
        $this->shortLabel = $shortLabel;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getParentOrgan(): ?self
    {
        return $this->parentOrgan;
    }

    public function setParentOrgan(?self $parentOrgan): static
    {
        $this->parentOrgan = $parentOrgan;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getOrgans(): Collection
    {
        return $this->organs;
    }

    public function addOrgan(self $organ): static
    {
        if (!$this->organs->contains($organ)) {
            $this->organs->add($organ);
            $organ->setParentOrgan($this);
        }

        return $this;
    }

    public function removeOrgan(self $organ): static
    {
        if ($this->organs->removeElement($organ)) {
            // set the owning side to null (unless already changed)
            if ($organ->getParentOrgan() === $this) {
                $organ->setParentOrgan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Actor>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): static
    {
        if (!$this->actors->contains($actor)) {
            $this->actors->add($actor);
            $actor->addOrgan($this);
        }

        return $this;
    }

    public function removeActor(Actor $actor): static
    {
        if ($this->actors->removeElement($actor)) {
            $actor->removeOrgan($this);
        }

        return $this;
    }
}
