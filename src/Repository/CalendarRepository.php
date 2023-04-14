<?php

namespace App\Repository;

use App\Entity\Users;
use App\Entity\Calendar;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Calendar>
 *
 * @method Calendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendar[]    findAll()
 * @method Calendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendar::class);
    }

    public function save(Calendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllByUserQueryBuilder(Users $user): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.name = :user')
            ->setParameter('user', $user)
            ->orderBy('c.start', 'ASC');
    }

    public function findAllByUser(Users $user): array
    {
        return $this->findAllByUserQueryBuilder($user)->getQuery()->getResult();
    }

    public function findAllByUserAndDateQueryBuilder(Users $user, \DateTimeInterface $date): QueryBuilder
    {
        return $this->findAllByUserQueryBuilder($user)
            ->andWhere('c.start = :date')
            ->setParameter('date', $date);
            
    }

    public function findAllByUserAndDate(Users $user, \DateTimeInterface $date): array
    {
        return $this->findAllByUserAndDateQueryBuilder($user, $date)->getQuery()->getResult();
    }

    public function findByUserOrAll(Users $user)
{
    $qb = $this->createQueryBuilder('c')
        ->andWhere('c.name = :user')
        ->setParameter('user', $user)
        ->orderBy('c.start', 'ASC')
        ->getQuery();

    return $qb->getResult();
}

// src/Repository/CalendarRepository.php
public function countReservationsAtDateTime(\DateTimeInterface $dateTime): int
{
    return $this->createQueryBuilder('c')
        ->select('COUNT(c.id)')
        ->where('c.start = :dateTime')
        ->setParameter('dateTime', $dateTime)
        ->getQuery()
        ->getSingleScalarResult();
}

public function findReservationsStartingInNext24Hours(): array
{
    $now = new \DateTimeImmutable();
    $in24Hours = $now->add(new \DateInterval('PT24H'));

    return $this->createQueryBuilder('c')
        ->andWhere('c.start >= :now')
        ->andWhere('c.start <= :in24Hours')
        ->setParameter('now', $now)
        ->setParameter('in24Hours', $in24Hours)
        ->getQuery()
        ->getResult();
}

}
