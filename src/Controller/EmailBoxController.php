<?php

namespace App\Controller;

use App\DTO\Request\ValidateEmailBoxRequestDto;
use App\DTO\Response\CreateEmailBoxResponseDto;
use App\DTO\Response\ReceivedEmailResponseDto;
use App\DTO\Response\ReceivedEmailResponseListDto;
use App\DTO\Response\ValidateEmailBoxResponseDto;
use App\Entity\TemporaryEmailBox;
use App\Repository\TemporaryEmailBoxRepository;
use App\Service\Client\ClientIpRetriever;
use App\Service\Client\CloudflareCountryRetriever;
use App\Service\Handler\ReceivedEmail\ReceivedEmailsFetcher;
use App\Service\Handler\ReceivedEmail\ReceivedEmailUpdater;
use App\Service\Handler\TemporaryEmailBox\CreateEmailBoxHandler;
use App\Service\Handler\TemporaryEmailBox\TemporaryEmailBoxFetcher;
use App\Service\Handler\TemporaryEmailBox\TemporaryEmailBoxUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class EmailBoxController extends AbstractController
{
    public function __construct(
        private TemporaryEmailBoxRepository $temporaryEmailBoxRepository,
        private CreateEmailBoxHandler $createEmailBoxHandler,
        private ReceivedEmailsFetcher $receivedEmailsFetcher,
        private ClientIpRetriever $clientIpRetriever,
        private ReceivedEmailUpdater $receivedEmailUpdater,
        private TemporaryEmailBoxFetcher $temporaryEmailBoxFetcher,
        private TemporaryEmailBoxUpdater $temporaryEmailBoxUpdater,
        private CloudflareCountryRetriever $cloudflareCountryRetriever,
    ) {
    }

    #[Route('/api/email-box', name: 'api_create_email_box', methods: ['POST'])]
    public function createEmailBox(Request $request): Response
    {
        $creatorIp = $this->clientIpRetriever->getClientIp($request);
        $countryCode = $this->cloudflareCountryRetriever->getCountryCode($request);

        $emailBox = $this->createEmailBoxHandler->create($creatorIp, $countryCode);

        return $this->json(CreateEmailBoxResponseDto::fromEntity($emailBox));
    }

    #[Route('/api/email-box/validate', name: 'api_email_box_validate', methods: ['POST'])]
    public function validateEmailBox(
        #[MapRequestPayload] ValidateEmailBoxRequestDto $validateEmailBoxRequestDto,
    ): Response
    {
        $emailBox = $this->temporaryEmailBoxRepository->findOneBy([
            'uuid' => $validateEmailBoxRequestDto->uuid,
            'email' => $validateEmailBoxRequestDto->email,
        ]);

        return $this->json(new ValidateEmailBoxResponseDto(
            is_valid: $emailBox instanceof TemporaryEmailBox,
        ));
    }

    #[Route('/api/email-box/{emailBoxUuid}/emails', name: 'api_get_email_box_messages', methods: ['GET'])]
    public function getEmailBoxByUuid(Uuid $emailBoxUuid): Response
    {
        $temporaryEmailBox = $this->temporaryEmailBoxFetcher->fetchByUuid($emailBoxUuid);
        $this->temporaryEmailBoxUpdater->markAsLastAccessedNow($temporaryEmailBox);

        $receivedEmails = $this->receivedEmailsFetcher->fetchByTemporaryEmailBox($temporaryEmailBox);

        $receivedEmailsResponseDtos = array_map(
            fn ($receivedEmail) => ReceivedEmailResponseListDto::fromEntity($receivedEmail),
            $receivedEmails
        );

        return $this->json($receivedEmailsResponseDtos);
    }

    #[Route('/api/email-box/{emailBoxUuid}/email/{emailUuid}', name: 'api_get_email_box_one_message', methods: ['GET'])]
    public function getOneReceivedEmailMessage(Uuid $emailBoxUuid, Uuid $emailUuid): Response
    {
        $receivedEmail = $this->receivedEmailsFetcher->findOneByTemporaryEmailBoxUuidAndReceivedEmailUuid($emailBoxUuid, $emailUuid);

        $this->receivedEmailUpdater->markEmailAsRead($receivedEmail);

        return $this->json(
            ReceivedEmailResponseDto::fromEntity(
                $receivedEmail
            )
        );
    }

    #[Route('/email-box/{emailBoxUuid}/email/{emailUuid}', name: 'api_view_email_box_one_message', methods: ['GET'])]
    public function viewOneReceivedEmailMessage(Uuid $emailBoxUuid, Uuid $emailUuid): Response
    {
        $receivedEmail = $this->receivedEmailsFetcher->findOneByTemporaryEmailBoxUuidAndReceivedEmailUuid($emailBoxUuid, $emailUuid);

        return $this->render('email_box/view.html.twig', [
            'receivedEmail' => $receivedEmail,
        ]);
    }
}
