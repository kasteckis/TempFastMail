<?php

namespace App\Entity;

use App\Repository\TemporaryEmailBoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TemporaryEmailBoxRepository::class)]
class TemporaryEmailBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'temporaryEmailBoxes')]
    private ?User $owner = null;

    /**
     * @var Collection<int, ReceivedEmail>
     */
    #[ORM\OneToMany(targetEntity: ReceivedEmail::class, mappedBy: 'temporaryEmailBox', orphanRemoval: true)]
    private Collection $receivedEmails;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $creatorIp = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(length: 255, unique: true)]
    private string $uuid;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastAccessedAt = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $countryCode = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
        $this->receivedEmails = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, ReceivedEmail>
     */
    public function getReceivedEmails(): Collection
    {
        return $this->receivedEmails;
    }

    public function addReceivedEmail(ReceivedEmail $receivedEmail): static
    {
        if (!$this->receivedEmails->contains($receivedEmail)) {
            $this->receivedEmails->add($receivedEmail);
            $receivedEmail->setTemporaryEmailBox($this);
        }

        return $this;
    }

    public function removeReceivedEmail(ReceivedEmail $receivedEmail): static
    {
        if ($this->receivedEmails->removeElement($receivedEmail)) {
            // set the owning side to null (unless already changed)
            if ($receivedEmail->getTemporaryEmailBox() === $this) {
                $receivedEmail->setTemporaryEmailBox(null);
            }
        }

        return $this;
    }

    public function getCreatorIp(): ?string
    {
        return $this->creatorIp;
    }

    public function setCreatorIp(?string $creatorIp): static
    {
        $this->creatorIp = $creatorIp;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getLastAccessedAt(): ?\DateTimeImmutable
    {
        return $this->lastAccessedAt;
    }

    public function setLastAccessedAt(?\DateTimeImmutable $lastAccessedAt): static
    {
        $this->lastAccessedAt = $lastAccessedAt;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }
}
