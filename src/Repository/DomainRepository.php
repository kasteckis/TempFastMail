<?php

namespace App\Repository;

use App\Entity\Domain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Domain>
 */
class DomainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domain::class);
    }

    public function countOfActiveDomains(): int
    {
        return (int) $this->createActiveDomainsQueryBuilder()
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<Domain>
     */
    public function findActiveDomains(): array
    {
        return $this->createActiveDomainsQueryBuilder()
            ->orderBy('d.domain', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneActiveByDomain(string $domain): ?Domain
    {
        return $this->createActiveDomainsQueryBuilder()
            ->andWhere('d.domain = :domain')
            ->setParameter('domain', $domain)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Premium domains are reserved for premium users, so they are never picked at random.
     */
    public function findOneActiveRandomNonPremiumDomain(): ?Domain
    {
        $count = (int) $this->createActiveNonPremiumDomainsQueryBuilder()
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($count === 0) {
            return null;
        }

        return $this->createActiveNonPremiumDomainsQueryBuilder()
            ->setMaxResults(1)
            ->setFirstResult(rand(0, $count - 1))
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function createActiveDomainsQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.activeUntil >= :now')
            ->setParameter('now', new \DateTimeImmutable());
    }

    private function createActiveNonPremiumDomainsQueryBuilder(): QueryBuilder
    {
        return $this->createActiveDomainsQueryBuilder()
            ->andWhere('d.premium = :premium')
            ->setParameter('premium', false);
    }
}
