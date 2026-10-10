<?php

namespace App\Exception\Domain;

use App\Exception\AbstractCustomException;
use App\Exception\ErrorCode;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deliberately generic: thrown for every rejected domain choice (not premium, unknown domain, inactive domain)
 * so the response never reveals whether a domain exists.
 */
class DomainNotAvailableException extends AbstractCustomException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function getErrorCode(): int
    {
        return ErrorCode::DOMAIN_NOT_AVAILABLE->value;
    }

    public function getErrorMessage(): string
    {
        return 'The requested domain is not available.';
    }
}
