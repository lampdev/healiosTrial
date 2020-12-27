<?php

namespace App\Repository;

use App\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, $this->getModel());
    }

    /**
     * @param $entity
     */
    public function plush(EntityInterface $entity): void
    {
        $manager = $this->getEntityManager();
        $manager->persist($entity);
        $manager->flush();
    }

    /**
     * @return string
     */
    abstract protected function getModel(): string;
}
