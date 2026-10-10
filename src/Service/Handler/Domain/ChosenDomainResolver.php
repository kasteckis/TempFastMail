<?php

namespace App\Service\Handler\Domain;

use App\Entity\Domain;
use App\Entity\User;
use App\Exception\Domain\DomainNotAvailableException;
use App\Repository\DomainRepository;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Resolves a domain explicitly chosen by the client.
 * Only logged-in users holding ROLE_PREMIUM may choose a domain, and only active domains are allowed.
 */
class ChosenDomainResolver
{
    public function __construct(
        private DomainRepository $domainRepository,
        private Security $security,
    ) {
    }

    public function resolve(string $domainName): Domain
    {
        // Non-premium users are rejected before any lookup, with the same generic error as an unknown domain,
        // so the response cannot be used to enumerate which domains exist.
        if (!$this->security->isGranted(User::ROLE_PREMIUM)) {
            throw new DomainNotAvailableException();
        }

        $domain = $this->domainRepository->findOneActiveByDomain($domainName);

        if ($domain === null) {
            throw new DomainNotAvailableException();
        }

        return $domain;
    }
}
