<?php

namespace App\Service\Handler\TemporaryEmailBox;

use App\Entity\Domain;
use App\Entity\TemporaryEmailBox;
use App\Exception\Domain\ThereAreNoDomainsException;
use App\Repository\DomainRepository;
use App\Service\Factory\TemporaryEmailBoxFactory;
use Doctrine\ORM\EntityManagerInterface;

class CreateEmailBoxHandler
{
    public function __construct(
        private DomainRepository $domainRepository,
        private TemporaryEmailBoxFactory $emailBoxFactory,
        private EntityManagerInterface $entityManager,
        private TemporaryEmailBoxGenerator $temporaryEmailBoxGenerator,
    ) {
    }

    /**
     * When no domain is given, a random active non-premium domain is used.
     * Callers are responsible for authorizing an explicitly chosen domain.
     */
    public function create(string $creatorIp, ?string $countryCode, ?Domain $domain = null): TemporaryEmailBox
    {
        $domain ??= $this->domainRepository->findOneActiveRandomNonPremiumDomain();

        if ($domain === null) {
            throw new ThereAreNoDomainsException();
        }

        $emailAddress = $this->temporaryEmailBoxGenerator->generateUniqueEmailAddress($domain->getDomain());

        $emailBox = $this->emailBoxFactory->create($emailAddress, $creatorIp, $countryCode);

        $this->entityManager->persist($emailBox);
        $this->entityManager->flush();

        return $emailBox;
    }
}
