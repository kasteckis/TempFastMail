<?php

namespace App\Exception\ReceivedEmail;

use App\Exception\AbstractCustomException;
use App\Exception\ErrorCode;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedToCreateReceivedEmailException extends AbstractCustomException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    public function getErrorCode(): int
    {
        return ErrorCode::UNAUTHORIZED_TO_CREATE_DOMAINS->value;
    }

    public function getErrorMessage(): string
    {
        return 'Bad Authorization header. Verify if Authorization header has correct value.';
    }
}
