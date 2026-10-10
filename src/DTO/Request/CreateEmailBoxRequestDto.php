<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateEmailBoxRequestDto
{
    public function __construct(
        #[Assert\Length(max: 255)]
        public readonly ?string $domain = null,
    ) {
    }
}
