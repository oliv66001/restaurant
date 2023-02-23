<?php

namespace App\Repository;

use App\Entity\Dishes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dishes>
 *
 * @method Dishes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dishes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dishes[]    findAll()
 * @method Dishes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dishes::class);
    }

    public function save(Dishes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dishes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findDishesPaginated(int $page, string $slug, int $limit = 1): array
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c', 'd')
            ->from('App\Entity\Dishes', 'd')
            ->join('d.categories', 'c')
            ->where("c.slug = :'$slug'")
            ->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit);

            dd($query->getQuery()->getResult());

            return $result;
    }

//    /**
//     * @return Dishes[] Returns an array of Dishes objects
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

//    public function findOneBySomeField($value): ?Dishes
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
