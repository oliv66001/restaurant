<?php

namespace App\Repository;

use App\Entity\BusinessHours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BusinessHours>
 *
 * @method BusinessHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusinessHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusinessHours[]    findAll()
 * @method BusinessHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BusinessHours::class);
    }

    public function save(BusinessHours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BusinessHours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrderedByDay(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT b FROM App\Entity\BusinessHours b ORDER BY b.day ASC, b.openTime ASC'
        );
    
        return $query->getResult();
    }
    

    public function findBusinessHoursByDay(string $day): ?BusinessHours
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT b
            FROM App\Entity\BusinessHours b
            WHERE b.day = :day'
        )->setParameter('day', $day);
    
        return $query->getOneOrNullResult();
    }

    public function findValidBusinessHoursForTime(\DateTime $dateTime): ?BusinessHours
{
    $dayOfWeek = $dateTime->format('l'); // Obtenir le jour de la semaine

    $entityManager = $this->getEntityManager();
    $query = $entityManager->createQuery(
        'SELECT b
        FROM App\Entity\BusinessHours b
        WHERE b.day = :day AND b.openTime <= :time AND b.closeTime >= :time'
    )
    ->setParameter('day', $dayOfWeek)
    ->setParameter('time', $dateTime->format('H:i:s'));

    return $query->getOneOrNullResult();
}


}
    
