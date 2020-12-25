<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @param string $name
     * @return Role|null
     */
    public function getOneByName(string $name): ?Role
    {
        try {
            $role = $this
                ->createQueryBuilder('role')
                ->andWhere('role.name = :name')
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            $role = null;
        }

        return $role;
    }

    /**
     * @param string $name
     * @return Role
     */
    public function createWithName(string $name): Role
    {
        $role = new Role();
        $role->setName($name);
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();

        return $role;
    }
}
