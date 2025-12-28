<?php

namespace App\Entity;

use App\Repository\TemporaryEmailBoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemporaryEmailBoxRepository::class)]
class TemporaryEmailBox
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $encryptedEmail = null;

    #[ORM\Column]
    private ?int $receivedEmailsCount = null;

    #[ORM\ManyToOne(inversedBy: 'temporaryEmailBoxes')]
    private ?User $owner = null;

    /**
     * @var Collection<int, ReceivedEmail>
     */
    #[ORM\OneToMany(targetEntity: ReceivedEmail::class, mappedBy: 'temporaryEmailBox')]
    private Collection $receivedEmails;

    public function __construct()
    {
        $this->receivedEmails = new ArrayCollection();
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

    public function getEncryptedEmail(): ?string
    {
        return $this->encryptedEmail;
    }

    public function setEncryptedEmail(string $encryptedEmail): static
    {
        $this->encryptedEmail = $encryptedEmail;

        return $this;
    }

    public function getReceivedEmailsCount(): ?int
    {
        return $this->receivedEmailsCount;
    }

    public function setReceivedEmailsCount(int $receivedEmailsCount): static
    {
        $this->receivedEmailsCount = $receivedEmailsCount;

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
}
