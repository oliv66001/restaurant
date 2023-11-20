<?php

namespace App\Repository;

use DateTime;
use App\Entity\Users;
use DateTimeInterface;
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
    // Total des places disponibles - À déplacer dans un fichier de configuration
    const TOTAL_AVAILABLE_PLACES = 30;
    const MAX_CAPACITY = 30;

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

    public function findAvailability(DateTimeInterface $start, int $numberOfGuests): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->andWhere('c.start = :start')
            ->setParameter('start', $start)
            ->andWhere('c.numberOfSeats >= :numberOfGuests')
            ->setParameter('numberOfGuests', $numberOfGuests)
            ->orderBy('c.start', 'ASC');
        
        $calendars = $qb->getQuery()->getResult();
        
        if (empty($calendars)) {
            return ['error' => 'Aucune disponibilité trouvée pour cette date.'];
        }

        $remainingPlaces = 0;
        foreach ($calendars as $calendar) {
            $remainingPlaces += $calendar->getNumberOfSeats();
        }

        $response = [
            'remainingPlaces' => $remainingPlaces,
        ];

        return $response;
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

    public function findAllByUserAndDateQueryBuilder(Users $user, DateTimeInterface $date): QueryBuilder
    {
        return $this->findAllByUserQueryBuilder($user)
            ->andWhere('c.start = :date')
            ->setParameter('date', $date);
    }

    public function countReservationsByDate(DateTimeInterface $dateTime): int
    {
        // Crée de nouveaux objets DateTime à partir de l'objet DateTimeInterface
        $dateTimeStart = new \DateTime($dateTime->format('Y-m-d H:i:s'));
        $dateTimeEnd = new \DateTime($dateTime->format('Y-m-d H:i:s'));
        
        // Réinitialise les minutes, les secondes et les microsecondes à 0 pour le début de l'heure
        $dateTimeStart->setTime($dateTimeStart->format('H'), 0, 0);
        
        // Réinitialise les minutes à 59 et les secondes à 59 pour la fin de l'heure
        $dateTimeEnd->setTime($dateTimeEnd->format('H'), 59, 59, 999999);
        
        $qb = $this->createQueryBuilder('c');
        
        $qb->select('COUNT(c.id)')
            ->where('c.start >= :dateTimeStart')
            ->andWhere('c.start <= :dateTimeEnd')
            ->setParameter('dateTimeStart', $dateTimeStart)
            ->setParameter('dateTimeEnd', $dateTimeEnd);
        
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    
    
    

    public function findAllByUserAndDate(Users $user, DateTimeInterface $date): array
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


    public function countReservationsAtDateTime(\DateTime $dateTime)
    {
        
        $startDateTime = \DateTime::createFromFormat('d/m/Y', $dateTime->format('d/m/Y'));
        $endDateTime = clone $startDateTime;
        $endDateTime->modify('+1 hour');

        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.start >= :startDateTime')
            ->andWhere('c.start < :endDateTime')
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime)
            ->getQuery();

        return $query->getSingleScalarResult();
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

    public function countReservationsByHour(\DateTime $startDateTime): int
    {
        $startDateTime = clone $startDateTime;
        $startDateTime->setTime($startDateTime->format('H'), 0, 0);
        $endDateTime = clone $startDateTime;
        $endDateTime->setTime($endDateTime->format('H'), 59, 59);
    
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->join('c.businessHours', 'b') // Jointure avec BusinessHours
            ->where('c.start >= :startDateTime')
            ->andWhere('c.start + b.closeTime <= :endDateTime') // Utilisation de closeTime de BusinessHours
            ->setParameter('startDateTime', $startDateTime)
            ->setParameter('endDateTime', $endDateTime)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    
    

    public function countByStart(\DateTimeInterface $start): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.start = :start')
            ->setParameter('start', $start);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByStart(\DateTimeInterface $start): ?Calendar
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.start = :start')
            ->setParameter('start', $start);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAvailableSlots(DateTimeInterface $start, DateTimeInterface $end): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.start >= :start')
            ->andWhere('c.end <= :end')
            ->andWhere('c.reservationCount < c.capacity')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        return $qb->getQuery()->getResult();
    }

    public function countOccupiedPlaces(?DateTimeInterface $start, int $numberOfGuests)
{
    if ($start === null) {
       
        return 30;
    }
    $from = new \DateTime($start->format('Y-m-d H:i:s'));
    $from->setTime($from->format('H'), 0, 0);

    $to = clone $from;
    $to->modify('+1 hour');

    $reservations = $this->createQueryBuilder('c')
        ->andWhere('c.start >= :from AND c.start < :to')
        ->setParameter('from', $from)
        ->setParameter('to', $to)
        ->getQuery()
        ->getResult();

    $occupiedPlaces = 0;
    foreach ($reservations as $reservation) {
        $occupiedPlaces += $reservation->getNumberOfGuests();
    }

    return $occupiedPlaces;
}

    
    
    
    public function getAvailablePlaces(?DateTimeInterface $dateTime, int $numberOfGuests): int
    {
        if ($dateTime === null) {
            // Retourne une valeur par défaut ou gère cette situation comme tu le souhaites
            return 30;
        }
        $occupiedPlaces = $this->countOccupiedPlaces($dateTime, $numberOfGuests);
        $availablePlaces = self::MAX_CAPACITY - $occupiedPlaces - $numberOfGuests;

        error_log("DateTime: " . $dateTime->format('Y-m-d H:i:s') . " - Available Places: " . $availablePlaces . " - Number of Guests: " . $numberOfGuests);
      
        return $availablePlaces;
    }

    public function findAllOrderByDate()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.start', 'ASC') // 'c.start' doit être remplacé par le nom de ton champ de date
            ->getQuery()
            ->getResult();
    }
}
