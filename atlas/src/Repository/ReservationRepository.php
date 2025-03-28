<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.dateArrivee BETWEEN :startDate AND :endDate')
            ->orWhere('r.dateDepart BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function findByRoomType(string $roomType): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.typeChambre = :roomType')
            ->setParameter('roomType', $roomType)
            ->getQuery()
            ->getResult();
    }

    public function findByEmail(string $email): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }
} 