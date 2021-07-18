<?php

namespace SoureCode\BundleTest\Tests;

use Nyholm\BundleTest\BaseBundleTestCase;
use SoureCode\BundleTest\CommandTrait;
use SoureCode\BundleTest\DatabaseTrait;
use SoureCode\BundleTest\KernelTrait;
use SoureCode\BundleTest\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

class ExampleTest extends BaseBundleTestCase
{
    use CommandTrait;
    use DatabaseTrait;
    use KernelTrait;
    use MailerAssertionsTrait;

    protected function getBundleClass(): string
    {
        return FrameworkBundle::class;
    }

    public function testTraitsCausesNoError(): void
    {
        static::assertSame(1, 1);
    }
}
