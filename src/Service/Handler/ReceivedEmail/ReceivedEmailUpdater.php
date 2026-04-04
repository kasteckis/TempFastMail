<?php

namespace App\Service\Handler\ReceivedEmail;

use App\Entity\ReceivedEmail;
use Doctrine\ORM\EntityManagerInterface;

class ReceivedEmailUpdater
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function markEmailAsRead(ReceivedEmail $receivedEmail): void
    {
        $receivedEmail->setReadAt(new \DateTimeImmutable());

        $this->entityManager->flush();
    }

    /**
     * @param ReceivedEmail[] $receivedEmails
     */
    public function markEmailsSubjectAsRead(array $receivedEmails): void
    {
        $now = new \DateTimeImmutable();

        foreach ($receivedEmails as $receivedEmail) {
            $receivedEmail->setSubjectReadAt($now);
        }

        $this->entityManager->flush();
    }
}
