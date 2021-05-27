<?php

namespace SoureCode\BundleTest;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;

trait DatabaseTrait
{
    protected static ?EntityManagerInterface $entityManager = null;

    /**
     * @param string[] $groups
     */
    protected function databaseLoadFixtures(array $groups, bool $append = false): void
    {
        if (class_exists(Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class)) {
            if (!method_exists($this, 'execute')) {
                throw new LogicException(
                    'Missing required CommandTrait, try to add the CommandTrait to your test class.'
                );
            }

            $this->execute(
                [
                    'command' => 'doctrine:fixtures:load',
                    '--no-interaction' => true,
                    '--append' => $append,
                    '--group' => $groups,
                ]
            );
        }
    }

    protected function setUpDatabase(bool $reset = true): void
    {
        /**
         * @var ContainerInterface $container
         */
        $container = static::$kernel->getContainer();

        $doctrine = $container->get('doctrine');

        static::$entityManager = $doctrine->getManager();

        if ($reset) {
            $this->databaseDrop();
            $this->databaseCreate();
            $this->databaseMigrate();
            $this->databaseUpdate();
        }
    }

    protected function databaseDrop(): void
    {
        if (!method_exists($this, 'execute')) {
            throw new LogicException('Missing required CommandTrait, try to add the CommandTrait to your test class.');
        }

        $this->execute(
            [
                'command' => 'doctrine:database:drop',
                '--force' => true,
                '--if-exists' => true,
            ]
        );
    }

    protected function databaseCreate(): void
    {
        if (!method_exists($this, 'execute')) {
            throw new LogicException('Missing required CommandTrait, try to add the CommandTrait to your test class.');
        }

        $this->execute(
            [
                'command' => 'doctrine:database:create',
                '--no-interaction' => true,
                '--if-not-exists' => true,
            ]
        );
    }

    protected function databaseMigrate(): void
    {
        if (class_exists(Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class)) {
            if (!method_exists($this, 'execute')) {
                throw new LogicException(
                    'Missing required CommandTrait, try to add the CommandTrait to your test class.'
                );
            }

            $this->execute(
                [
                    'command' => 'doctrine:migrations:migrate',
                    '--no-interaction' => true,
                ]
            );
        }
    }

    protected function databaseUpdate(): void
    {
        if (!method_exists($this, 'execute')) {
            throw new LogicException('Missing required CommandTrait, try to add the CommandTrait to your test class.');
        }

        $this->execute(
            [
                'command' => 'doctrine:schema:update',
                '--force' => true,
                '--no-interaction' => true,
            ]
        );
    }

    protected function getEntityManager()
    {
        return static::$entityManager;
    }

    protected function tearDownDatabase(): void
    {
        static::$entityManager->close();
        static::$entityManager = null; // avoid memory leaks
    }
}
