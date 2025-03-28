<?php

namespace App\Repository;

use App\Entity\Airline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Airline>
 *
 * @method Airline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Airline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Airline[]    findAll()
 * @method Airline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AirlineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Airline::class);
    }

    public function findByCountry(string $country): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.pays = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();
    }

    public function findByName(string $name): ?Airline
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nom = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 