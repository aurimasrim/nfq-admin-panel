<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByNotInGroup(int $groupId): array
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT u
                FROM App:User u
                WHERE u NOT IN (
                  SELECT us
                  FROM App:User us
                  JOIN us.groups g
                  WHERE g.id = :groupId
                )
            ')->setParameter('groupId', $groupId)->getResult();
    }
}
