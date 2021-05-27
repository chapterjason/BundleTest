<?php

namespace SoureCode\BundleTest;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

trait CommandTrait
{
    protected static ?Application $application = null;

    public function execute(array $input): void
    {
        $output = new BufferedOutput();
        $exitCode = static::$application->run(new ArrayInput($input), $output);

        if (0 !== $exitCode) {
            $content = $output->fetch();
            $consoleException = new RuntimeException($content);

            throw new RuntimeException(sprintf('Could not run command "%s". %s', serialize($input), $content), $exitCode, $consoleException);
        }
    }

    protected function setUpCommand(): void
    {
        static::$application = new Application(static::$kernel);
        static::$application->setAutoExit(false);
    }

    protected function tearDownCommand(): void
    {
        static::$application = null;
    }
}
