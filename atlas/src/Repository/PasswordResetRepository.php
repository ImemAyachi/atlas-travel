<?php

namespace App\Repository;

use App\Entity\PasswordReset;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordReset>
 *
 * @method PasswordReset|null find($id, $lockMode = null, $lockVersion = null)
 * @method PasswordReset|null findOneBy(array $criteria, array $orderBy = null)
 * @method PasswordReset[]    findAll()
 * @method PasswordReset[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function findValidToken(string $token): ?PasswordReset
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.token = :token')
            ->andWhere('pr.expiresAt > :now')
            ->andWhere('pr.isUsed = :isUsed')
            ->setParameter('token', $token)
            ->setParameter('now', new \DateTime())
            ->setParameter('isUsed', false)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUnusedTokenByUser(int $userId): ?PasswordReset
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.user = :userId')
            ->andWhere('pr.isUsed = :isUsed')
            ->andWhere('pr.expiresAt > :now')
            ->setParameter('userId', $userId)
            ->setParameter('isUsed', false)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function cleanExpiredTokens(): int
    {
        return $this->createQueryBuilder('pr')
            ->delete()
            ->where('pr.expiresAt <= :now')
            ->orWhere('pr.isUsed = :isUsed')
            ->setParameter('now', new \DateTime())
            ->setParameter('isUsed', true)
            ->getQuery()
            ->execute();
    }

    public function findUnusedTokensByUser(User $user): array
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.user = :user')
            ->andWhere('pr.isUsed = :isUsed')
            ->setParameter('user', $user)
            ->setParameter('isUsed', false)
            ->getQuery()
            ->getResult();
    }

    public function cleanupExpiredTokens(): int
    {
        return $this->createQueryBuilder('pr')
            ->delete()
            ->where('pr.expiresAt < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->execute();
    }
} 