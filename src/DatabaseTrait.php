<?php

namespace SoureCode\Component\Test;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

trait DatabaseTrait
{
    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param string|null     $managerName
     *
     * @return EntityRepository<T>
     */
    protected function getRepository(string $className, string $managerName = null): EntityRepository
    {
        $manager = $this->getEntityManager($managerName);

        return $manager->getRepository($className);
    }

    protected function getEntityManager($managerName = null): EntityManagerInterface
    {
        $doctrine = $this->getDoctrine();
        $managerName = $managerName ?? $doctrine->getDefaultManagerName();

        return $doctrine->getManager($managerName);
    }

    protected function getDoctrine(): Registry
    {
        $container = static::getContainer();

        return $container->get('doctrine');
    }
}
