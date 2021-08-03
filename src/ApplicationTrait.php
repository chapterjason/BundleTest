<?php

namespace SoureCode\Component\Test;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Contracts\Service\ResetInterface;

trait ApplicationTrait
{
    /**
     * @var class-string<Application>
     */
    protected static ?string $applicationClass = null;

    protected static ?Application $application = null;

    protected function executeCommand(array $input): string
    {
        if (!static::$application) {
            self::bootApplication();
        }

        $outputBuffer = new BufferedOutput();
        $exitCode = static::$application->run(new ArrayInput($input), $outputBuffer);
        $output = $outputBuffer->fetch();

        if (0 !== $exitCode) {
            $consoleException = new RuntimeException($output);
            $exceptionMessage = sprintf(
                'Could not run command "%s". %s',
                print_r($input, true),
                $output
            );

            throw new RuntimeException($exceptionMessage, $exitCode, $consoleException);
        }

        return $output;
    }

    protected function tearDown(): void
    {
        static::ensureApplicationShutdown();
        static::$application = null;

        parent::tearDown();
    }

    protected static function bootApplication(array $options = []): ?Application
    {
        if (null !== static::$application) {
            static::ensureApplicationShutdown();
        }

        if (!static::$booted) {
            self::bootKernel();
        }

        static::$application = static::createApplication(
            array_merge(
                [
                    'kernel' => static::$kernel,
                ],
                $options,
            )
        );

        return static::$application;
    }

    protected static function ensureApplicationShutdown(): void
    {
        if (self::$application instanceof ResetInterface) {
            self::$application->reset();
        }
    }

    protected static function createApplication(array $options = [])
    {
        // Use the static, if not set use default Application
        $applicationClass = static::$applicationClass ?? Application::class;

        // Allow override by options
        if (array_key_exists('applicationClass', $options)) {
            $applicationClass = $options['applicationClass'];
        }

        // Validate application class
        if (!class_implements($applicationClass, Application::class)) {
            throw new InvalidArgumentException(sprintf('Given application class musst implement %s', Application::class));
        }

        // Set application class if not set
        if (null === static::$applicationClass) {
            static::$applicationClass = $applicationClass;
        }

        // Get or boot kernel if required
        $kernel = array_key_exists('kernel', $options) ? $options['kernel'] : static::bootKernel($options);

        $application = new $applicationClass($kernel);

        // Important cause the test run will be exited if a command will fail.
        $application->setAutoExit(false);

        return $application;
    }
}
