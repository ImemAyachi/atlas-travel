<?php

namespace App\Repository;

use App\Entity\Flight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flight>
 *
 * @method Flight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flight[]    findAll()
 * @method Flight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    public function findByRoute(string $departure, string $destination): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.departure = :departure')
            ->andWhere('f.destination = :destination')
            ->setParameter('departure', $departure)
            ->setParameter('destination', $destination)
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.departureDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function findAvailableFlights(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.availableSeats > 0')
            ->andWhere('f.departureDate >= :today')
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getResult();
    }
} 