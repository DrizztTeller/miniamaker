<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Discussion;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Discussion>
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    //    /**
    //     * @return Discussion[] Returns an array of Discussion objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Discussion
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function FindAllRecentDiscussions(User $user, DateTime $dateThreshold)
    {
        return $this->createQueryBuilder('d')
            ->innerJoin('d.messages', 'm')
            ->where('d.sender = :user')
            ->orWhere('d.receiver = :user')
            ->andWhere('m.created_at > :dateThreshold')
            ->setParameter('user', $user)
            ->setParameter('dateThreshold', $dateThreshold)
            ->orderBy('m.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
