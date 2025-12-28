<?php

namespace App\Service\Factory;

use App\DTO\Request\CreateEmailRequestDto;
use App\Entity\ReceivedEmail;
use App\Entity\TemporaryEmailBox;
use App\Repository\TemporaryEmailBoxRepository;

class ReceivedEmailFactory
{
    public function __construct(
        private TemporaryEmailBoxRepository $temporaryEmailBoxRepository,
    ) {
    }

    public function createFromDto(CreateEmailRequestDto $dto): ReceivedEmail
    {
        $email = new ReceivedEmail();

        $email
            ->setRealFrom($dto->real_from)
            ->setRealTo($dto->real_to)
            ->setSubject($dto->subject)
            ->setFromName($dto->from_name)
            ->setFromAddress($dto->from_address)
            ->setToMultiple($dto->to_multiple ?? [])
            ->setBccMultiple($dto->bcc_multiple ?? [])
            ->setHtml($dto->html)
            ->setMetadata($dto->metadata ?? []);


        $temporaryEmailBox = $this->temporaryEmailBoxRepository->findOneBy(['email' => $dto->real_to]);

        if ($temporaryEmailBox instanceof TemporaryEmailBox) {
            $email->setTemporaryEmailBox($temporaryEmailBox);
        }

        return $email;
    }
}
