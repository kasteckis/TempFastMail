<?php

namespace App\DTO\Response;

use App\Entity\Domain;

class DomainResponseDto
{
    public function __construct(
        public string $domain,
        public bool $premium,
    ) {
    }

    public static function fromEntity(Domain $domain): self
    {
        return new self(
            domain: $domain->getDomain(),
            premium: $domain->isPremium(),
        );
    }
}
