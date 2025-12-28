<?php

namespace App\Entity;

use App\Repository\DomainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
class Domain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $domain = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $activeUntil = null;

    public function __construct()
    {
        $this->activeUntil = new \DateTimeImmutable('+1 year');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getActiveUntil(): ?\DateTimeImmutable
    {
        return $this->activeUntil;
    }

    public function setActiveUntil(\DateTimeImmutable $activeUntil): static
    {
        $this->activeUntil = $activeUntil;

        return $this;
    }
}
