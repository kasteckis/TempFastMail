<?php

namespace App\Entity;

use App\Repository\ReceivedEmailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceivedEmailRepository::class)]
class ReceivedEmail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $realFrom = null;

    #[ORM\Column(length: 255)]
    private ?string $realTo = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $fromName = null;

    #[ORM\Column(length: 255)]
    private ?string $fromAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $bccName = null;

    #[ORM\Column(length: 255)]
    private ?string $bccAddress = null;

    #[ORM\Column]
    private array $toMultiple = [];

    #[ORM\Column(type: Types::TEXT)]
    private ?string $html = null;

    #[ORM\Column]
    private array $metadata = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealFrom(): ?string
    {
        return $this->realFrom;
    }

    public function setRealFrom(string $realFrom): static
    {
        $this->realFrom = $realFrom;

        return $this;
    }

    public function getRealTo(): ?string
    {
        return $this->realTo;
    }

    public function setRealTo(string $realTo): static
    {
        $this->realTo = $realTo;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): static
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(string $fromAddress): static
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getBccName(): ?string
    {
        return $this->bccName;
    }

    public function setBccName(string $bccName): static
    {
        $this->bccName = $bccName;

        return $this;
    }

    public function getBccAddress(): ?string
    {
        return $this->bccAddress;
    }

    public function setBccAddress(string $bccAddress): static
    {
        $this->bccAddress = $bccAddress;

        return $this;
    }

    public function getToMultiple(): array
    {
        return $this->toMultiple;
    }

    public function setToMultiple(array $toMultiple): static
    {
        $this->toMultiple = $toMultiple;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
