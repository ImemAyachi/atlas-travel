<?php

namespace App\Repository;

use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 *
 * @method Package|null find($id, $lockMode = null, $lockVersion = null)
 * @method Package|null findOneBy(array $criteria, array $orderBy = null)
 * @method Package[]    findAll()
 * @method Package[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function findByPriceRange(float $minPrice, float $maxPrice): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price BETWEEN :minPrice AND :maxPrice')
            ->setParameter('minPrice', $minPrice)
            ->setParameter('maxPrice', $maxPrice)
            ->getQuery()
            ->getResult();
    }

    public function findByDuration(int $duration): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.duration = :duration')
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }

    public function findAvailablePackages(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.availableSeats > 0')
            ->getQuery()
            ->getResult();
    }
} 