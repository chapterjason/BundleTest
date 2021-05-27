<?php

namespace SoureCode\Bundle\Token\Tests\TestUtils;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use function in_array;
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
                throw new LogicException('Missing required CommandTrait, try to add the CommandTrait to your test class.');
            }

            // Programmatically to complicated
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
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $databaseName = $connection->getDatabase();

        $exist = in_array($databaseName, $schemaManager->listDatabases());

        if ($exist) {
            $schemaManager->dropDatabase($databaseName);
        }
    }

    protected function getEntityManager()
    {
        return static::$entityManager;
    }

    protected function databaseCreate(): void
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $databaseName = $connection->getDatabase();

        $exist = in_array($databaseName, $schemaManager->listDatabases());

        if (!$exist) {
            $schemaManager->createDatabase($databaseName);
        }
    }

    protected function databaseMigrate(): void
    {
        if (class_exists(Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class)) {
            if (!method_exists($this, 'execute')) {
                throw new LogicException('Missing required CommandTrait, try to add the CommandTrait to your test class.');
            }

            // Programmatically to complicated
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
        $manager = $this->getEntityManager();
        $schemaTool = new SchemaTool($manager);

        $metadata = $manager->getMetadataFactory()->getAllMetadata();

        $schemaTool->updateSchema($metadata, true);
    }

    protected function tearDownDatabase(): void
    {
        static::$entityManager->close();
        static::$entityManager = null; // avoid memory leaks
    }
}
