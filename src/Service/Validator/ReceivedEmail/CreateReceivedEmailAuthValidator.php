<?php

namespace App\Service\Validator\ReceivedEmail;

use App\Exception\ReceivedEmail\UnauthorizedToCreateReceivedEmailException;
use Symfony\Component\HttpFoundation\Request;

class CreateReceivedEmailAuthValidator
{
    public function validate(Request $request): void
    {
        if ($request->headers->get('Authorization') === $_ENV['CREATE_RECEIVED_EMAIL_API_AUTHORIZATION_KEY']) {
            return;
        }

        throw new UnauthorizedToCreateReceivedEmailException();
    }
}
