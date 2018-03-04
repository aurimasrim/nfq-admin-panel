<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findAll(): array
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT g, u
                FROM App:Group g
                LEFT JOIN g.users u
            ')->getResult();
    }

    public function findById(int $groupId): ?Group
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT g, u
                FROM App:Group g
                LEFT JOIN g.users u
                WHERE g.id = :groupId
            ')->setParameter('groupId', $groupId)->getOneOrNullResult();
    }
}
