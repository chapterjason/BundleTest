<?php

namespace SoureCode\Component\Test\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SoureCode\Component\Test\DatabaseTrait;
use SoureCode\Component\Test\Tests\Fixtures\App\WorkaroundKernel;
use SoureCode\Component\Test\Tests\Fixtures\FooBundle\Entity\Book;
use SoureCode\Component\Test\Tests\Fixtures\FooBundle\FooBundle;
use SoureCode\Component\Test\Tests\Fixtures\FooBundle\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DatabaseTraitTest extends KernelTestCase
{
    use DatabaseTrait;

    protected static function createKernel(array $options = [])
    {
        /**
         * @var WorkaroundKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testGetDoctrine()
    {
        // Act
        $doctrine = $this->getDoctrine();

        // Assert
        self::assertNotNull($doctrine);
        self::assertInstanceOf(Registry::class, $doctrine);
    }

    public function testGetRepository()
    {
        // Act
        $repository = $this->getRepository(Book::class);

        // Assert
        self::assertNotNull($repository);
        self::assertInstanceOf(BookRepository::class, $repository);
    }

    protected function setUp(): void
    {
        KernelTestCase::$class = WorkaroundKernel::class;

        self::bootKernel([
            'configFiles' => [
                __DIR__.'/Fixtures/App/config/config.yml',
            ],
            'projectDir' => __DIR__.'/Fixtures/App',
            'bundles' => [
                DoctrineBundle::class,
                FooBundle::class,
            ],
        ]);
    }

}
