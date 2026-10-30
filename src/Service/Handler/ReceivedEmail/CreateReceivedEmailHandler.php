<?php

namespace App\Service\Handler\ReceivedEmail;

use App\DTO\Request\CreateEmailRequestDto;
use App\Entity\ReceivedEmail;
use App\Service\Factory\ReceivedEmailFactory;
use App\Service\Realtime\ReceivedEmailRealtimeNotifier;
use Doctrine\ORM\EntityManagerInterface;

class CreateReceivedEmailHandler
{
    public function __construct(
        private ReceivedEmailFactory $receivedEmailFactory,
        private EntityManagerInterface $entityManager,
        private ReceivedEmailRealtimeNotifier $receivedEmailRealtimeNotifier,
    ) {
    }

    public function create(CreateEmailRequestDto $createEmailRequestDto): ReceivedEmail
    {
        $receivedEmail = $this->receivedEmailFactory->createFromDto($createEmailRequestDto);

        $this->entityManager->persist($receivedEmail);
        $this->entityManager->flush();

        $this->receivedEmailRealtimeNotifier->notify($receivedEmail);

        return $receivedEmail;
    }
}
