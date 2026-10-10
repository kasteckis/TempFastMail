<?php

namespace App\Repository;

use App\Entity\TemporaryEmailBox;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TemporaryEmailBox>
 */
class TemporaryEmailBoxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemporaryEmailBox::class);
    }

    public function findOlderThan(\DateTimeImmutable $olderThan): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.createdAt < :olderThan')
            ->setParameter('olderThan', $olderThan)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<array{countryCode: string, emailBoxCount: int|string}>
     */
    public function findTopCountriesByEmailBoxCount(int $limit = 5): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.countryCode AS countryCode, COUNT(t.id) AS emailBoxCount')
            ->where('t.countryCode IS NOT NULL')
            ->groupBy('t.countryCode')
            ->orderBy('emailBoxCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
