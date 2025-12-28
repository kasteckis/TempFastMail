<?php

namespace App\Controller;

use App\DTO\Response\CreateEmailBoxResponseDto;
use App\DTO\Response\ReceivedEmailResponseDto;
use App\Service\Handler\CreateEmailBoxHandler;
use App\Service\ReceivedEmail\ReceivedEmailsFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class EmailBoxController extends AbstractController
{
    public function __construct(
        private CreateEmailBoxHandler $createEmailBoxHandler,
        private ReceivedEmailsFetcher $receivedEmailsFetcher,
    ) {
    }

    #[Route('/api/email-box', name: 'api_create_email_box', methods: ['POST'])]
    public function createEmailBox(Request $request): Response
    {
        $creatorIp = $request->getClientIp() ?? 'unknown';

        $emailBox = $this->createEmailBoxHandler->create($creatorIp);

        return $this->json(CreateEmailBoxResponseDto::fromEntity($emailBox));
    }

    #[Route('/api/email-box/{uuid}/emails', name: 'api_get_email_box_messages', methods: ['GET'])]
    public function getEmailBoxByUuid(Uuid $uuid): Response
    {
        $receivedEmails = $this->receivedEmailsFetcher->fetchByTemporaryEmailBoxUuid($uuid);

        $receivedEmailsResponseDtos = array_map(
            fn ($receivedEmail) => ReceivedEmailResponseDto::fromEntity($receivedEmail),
            $receivedEmails
        );

        return $this->json($receivedEmailsResponseDtos);
    }
}
