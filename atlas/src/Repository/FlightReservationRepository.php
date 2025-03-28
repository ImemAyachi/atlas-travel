<?php

namespace App\Repository;

use App\Entity\FlightReservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FlightReservation>
 *
 * @method FlightReservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlightReservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlightReservation[]    findAll()
 * @method FlightReservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlightReservation::class);
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.reservationStatus = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findByPaymentStatus(string $status): array
    {
        return $this->createQueryBuilder('fr')
            ->andWhere('fr.paymentStatus = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
} 