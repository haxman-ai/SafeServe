<?php

namespace App\Entity;
use App\Entity\Plat;
use App\Repository\TempRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TempRepository::class)]
class Temp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plat $plat = null;


    public function getPlat(): ?Plat
    {
        return $this->plat;
    }

    public function setPlat(?Plat $plat): static
    {
         $this->plat = $plat;
         return $this;
    }


    #[ORM\Column]
    private ?float $temperature = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $releveAT = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getReleveAT(): ?\DateTimeImmutable
    {
        return $this->releveAT;
    }

    public function setReleveAT(\DateTimeImmutable $releveAT): static
    {
        $this->releveAT = $releveAT;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
