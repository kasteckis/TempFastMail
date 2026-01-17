<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    public function findWithOffsetAndLimit(int $offset, int $limit): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getSuggestedBlogs(Blog $blog): array
    {
        $allBlogCount = $this->count([]);

        if ($allBlogCount < 4) {
            return [];
        }

        $randomOffset = rand(0, $allBlogCount - 3 - 1);

        return $this->createQueryBuilder('b')
            ->where('b.id != :id')
            ->setParameter('id', $blog->getId())
            ->setMaxResults(4)
            ->setFirstResult($randomOffset)
            ->getQuery()
            ->getResult()
        ;
    }
}
