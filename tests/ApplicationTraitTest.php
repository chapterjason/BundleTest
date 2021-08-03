<?php

namespace SoureCode\Component\Test\Tests;

use InvalidArgumentException;
use SoureCode\Component\Test\ApplicationTrait;
use SoureCode\Component\Test\Tests\Fixtures\App\WorkaroundKernel;
use SoureCode\Component\Test\Tests\Fixtures\BarApplication;
use SoureCode\Component\Test\Tests\Fixtures\FooApplication;
use SoureCode\Component\Test\Tests\Fixtures\InvalidApplication;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\RuntimeException;

class ApplicationTraitTest extends KernelTestCase
{
    use ApplicationTrait;

    public function testExecute(): void
    {
        $output = $this->executeCommand(['help']);

        self::assertStringContainsString('Display help for a command', $output);
    }

    public function testFailingExecute()
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Act
        $this->executeCommand(['list', '--invalid-option' => true]);
    }

    public function testCreateApplication(): void
    {
        // Act
        $application = static::createApplication();

        // Assert
        self::assertInstanceOf(Application::class, $application);
    }

    public function testCreateApplicationWithApplicationClassInOptions()
    {
        // Act
        $application = static::createApplication([
            'applicationClass' => FooApplication::class,
        ]);

        // Assert
        self::assertInstanceOf(FooApplication::class, $application);
    }

    public function testThrowIfApplicationClassIsInvalid()
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Given application class musst implement %s', Application::class));

        // Act
        static::createApplication([
            'applicationClass' => InvalidApplication::class,
        ]);
    }

    public function testCreateApplicationWithStaticApplicationClass()
    {
        // Arrange
        static::$applicationClass = BarApplication::class;

        // Act
        $application = static::createApplication();

        // Assert
        self::assertInstanceOf(BarApplication::class, $application);
    }

    protected function setUp(): void
    {
        static::$class = WorkaroundKernel::class;
    }
}
