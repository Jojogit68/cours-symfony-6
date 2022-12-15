<?php

namespace App\Repository;

use App\Entity\Annonce;
use App\Entity\AnnonceSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Annonce>
 *
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function save(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Annonce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Annonce[]
     */
    public function findAllNotSold(): array
    {
        return $this->findNotSoldQuery()
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Annonce[]
     */
    public function findLatestNotSold(): array
    {
        return $this->findNotSoldQuery()
            ->setMaxResults(3)
            ->orderBy('a.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return QueryBuilder
     */
    private function findNotSoldQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isSold = false')
        ;
    }

    public function findAllNotSoldQuery(): Query
    {
        return $this->findNotSoldQuery()
            ->getQuery()
        ;
    }

    public function findByAnnonceSearchQuery(AnnonceSearch $annonceSearch): Query
    {
        $query = $this->createQueryBuilder('a');
        if ($annonceSearch->getCreatedAt() !== null) {
            $query
                ->andWhere('a.createdAt > :createdAt')
                ->setParameter(':createdAt', $annonceSearch->getCreatedAt())
            ;
        }

        if ($annonceSearch->getTitle() !== null) {
            $query
                ->andWhere('a.title LIKE :title')
                ->setParameter('title', '%'.$annonceSearch->getTitle().'%')
            ;
        }

        if ($annonceSearch->getStatus() !== null) {
            $query
                ->andWhere('a.status = :status')
                ->setParameter('status', $annonceSearch->getStatus())
            ;
        }

        if ($annonceSearch->getMaxPrice() !== null) {
            $query
                ->andWhere('a.price < :maxPrice')
                ->setParameter('maxPrice', $annonceSearch->getMaxPrice())
            ;
        }

        if ($annonceSearch->getTags()->count() > 0) {
            $cpt = 0;
            foreach ($annonceSearch->getTags() as $key => $tag) {
                $query = $query
                    ->andWhere(':tag'.$cpt.' MEMBER OF a.tags')
                    ->setParameter('tag'.$cpt, $tag);
                $cpt++;
            }
        }

        return $query->getQuery();
    }

//    /**
//     * @return Annonce[] Returns an array of Annonce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Annonce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
