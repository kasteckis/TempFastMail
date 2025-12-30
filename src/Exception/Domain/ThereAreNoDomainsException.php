<?php

namespace App\Exception\Domain;

use App\Exception\AbstractCustomException;
use App\Exception\ErrorCode;
use Symfony\Component\HttpFoundation\Response;

class ThereAreNoDomainsException extends AbstractCustomException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): int
    {
        return ErrorCode::THERE_ARE_NO_DOMAINS->value;
    }

    public function getErrorMessage(): string
    {
        return 'There are no assigned domains';
    }
}
