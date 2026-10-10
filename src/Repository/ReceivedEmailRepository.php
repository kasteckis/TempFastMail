<?php

namespace App\Repository;

use App\Entity\ReceivedEmail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceivedEmail>
 */
class ReceivedEmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceivedEmail::class);
    }

    public function deleteOlderThan(\DateTimeImmutable $cutoffDate): int
    {
        return $this->createQueryBuilder('r')
            ->delete()
            ->where('r.createdAt < :cutoffDate')
            ->setParameter('cutoffDate', $cutoffDate)
            ->getQuery()
            ->execute();
    }

    public function countWithTemporaryEmailBox(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.temporaryEmailBox IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countWithoutTemporaryEmailBox(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.temporaryEmailBox IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countReadEmails(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.readAt IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countUnreadEmails(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.readAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countEmailWithReadSubjects(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.subjectReadAt IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByDomain(string $domain): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.realTo LIKE :domain')
            ->setParameter('domain', '%@' . $domain)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<array{countryCode: string, receivedEmailCount: int|string}>
     */
    public function findTopCountriesByReceivedEmailCount(int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->select('t.countryCode AS countryCode, COUNT(r.id) AS receivedEmailCount')
            ->innerJoin('r.temporaryEmailBox', 't')
            ->where('t.countryCode IS NOT NULL')
            ->groupBy('t.countryCode')
            ->orderBy('receivedEmailCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
