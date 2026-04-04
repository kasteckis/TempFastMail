<?php

namespace App\Service\Handler\ReceivedEmail;

use App\Repository\ReceivedEmailRepository;

class DeleteOldReceivedEmailsHandler
{
    public function __construct(
        private ReceivedEmailRepository $receivedEmailRepository,
    ) {
    }

    public function deleteOlderThan(\DateTimeImmutable $olderThan): int
    {
        return $this->receivedEmailRepository->deleteOlderThan($olderThan);
    }
}
