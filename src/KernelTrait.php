<?php

namespace SoureCode\Bundle\Token\Tests\TestUtils;

use Symfony\Component\HttpKernel\KernelInterface;

trait KernelTrait
{
    protected static ?KernelInterface $kernel = null;

    protected function setUpKernel(KernelInterface $kernel): void
    {
        static::$kernel = $kernel;

        $this->configureKernel();

        $kernel->boot();
    }

    protected function configureKernel(): void
    {
    }

    protected function tearDownKernel(): void
    {
        $this->ensureKernelShutdown();

        static::$kernel = null;
    }
}
