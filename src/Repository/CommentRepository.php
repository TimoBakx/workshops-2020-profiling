<?php

namespace App\Repository;

use App\Entity\BigFootSighting;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function countForUser(User $user, \DateTimeImmutable $sinceDate): int
    {
        return (int) $this->createQueryBuilder('comment')
            ->select('COUNT(comment.id)')
            ->andWhere('comment.owner = :user')
            ->andWhere('comment.createdAt >= :sinceDate')
            ->setParameter('user', $user)
            ->setParameter('sinceDate', $sinceDate)
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getCommentsForSighting(BigFootSighting $sighting)
    {
        return $this->createQueryBuilder('c')
            ->select(['c.createdAt', 'c.content'])
            ->addSelect('o.username AS owner_username')
            ->addSelect('o.email AS owner_email')
            ->addSelect('COUNT(oc.id) AS owner_activity_count')
            ->join('c.owner', 'o')
            ->join('o.comments', 'oc', Join::WITH, 'oc.createdAt >= :commentCountLimit')
            ->where('c.bigFootSighting = :sighting')
            ->setParameter('sighting', $sighting)
            ->setParameter('commentCountLimit', new \DateTimeImmutable('-3 months'))
            ->groupBy('c, o')
            ->getQuery()
            ->getResult();
    }
}
