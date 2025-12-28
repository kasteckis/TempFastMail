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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fromName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fromAddress = null;

    #[ORM\Column]
    private array $toMultiple = [];

    #[ORM\Column]
    private array $bccMultiple = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $html = null;

    #[ORM\Column]
    private array $metadata = [];

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'receivedEmails')]
    private ?TemporaryEmailBox $temporaryEmailBox = null;

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

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getFromName(): ?string
    {
        return $this->fromName;
    }

    public function setFromName(?string $fromName): static
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromAddress(): ?string
    {
        return $this->fromAddress;
    }

    public function setFromAddress(?string $fromAddress): static
    {
        $this->fromAddress = $fromAddress;

        return $this;
    }

    public function getToMultiple(): array
    {
        return $this->toMultiple;
    }

    public function getToMultipleString(): string
    {
        return json_encode($this->toMultiple);
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

    public function setHtml(?string $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getMetadataString(): string
    {
        return json_encode($this->metadata);
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

    public function getBccMultiple(): array
    {
        return $this->bccMultiple;
    }

    public function getBccMultipleString(): string
    {
        return json_encode($this->bccMultiple);
    }

    public function setBccMultiple(array $bccMultiple): static
    {
        $this->bccMultiple = $bccMultiple;

        return $this;
    }

    public function getTemporaryEmailBox(): ?TemporaryEmailBox
    {
        return $this->temporaryEmailBox;
    }

    public function setTemporaryEmailBox(?TemporaryEmailBox $temporaryEmailBox): static
    {
        $this->temporaryEmailBox = $temporaryEmailBox;

        return $this;
    }

    public function getHasAssignedTemporaryEmailBox(): string
    {
        if ($this->temporaryEmailBox === null) {
            return 'No';
        }

        return 'Yes';
    }
}
