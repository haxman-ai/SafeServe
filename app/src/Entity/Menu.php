<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $served_at = null;

    #[ORM\ManyToMany(targetEntity: Plat::class)]
    private Collection $plats;

    public function __construct()
    {
        $this->plats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServedAt(): ?\DateTimeImmutable
    {
        return $this->served_at;
    }

    public function setServedAt(\DateTimeImmutable $served_at): static
    {
        $this->served_at = $served_at;

        return $this;
    }

    /** @return Collection<int, Plat> */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
        }
        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        $this->plats->removeElement($plat);
        return $this;
    }

    public function getPlatsByType(string $type): Collection
    {
        return $this->plats->filter(fn(Plat $p) => $p->getType() === $type);
    }
}
