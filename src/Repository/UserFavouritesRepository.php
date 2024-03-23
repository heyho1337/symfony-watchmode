<?php

namespace App\Repository;

use App\Entity\UserFavourites;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserFavourites>
 *
 * @method UserFavourites|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFavourites|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFavourites[]    findAll()
 * @method UserFavourites[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFavouritesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFavourites::class);
    }

    //    /**
    //     * @return UserFavourites[] Returns an array of UserFavourites objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserFavourites
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
