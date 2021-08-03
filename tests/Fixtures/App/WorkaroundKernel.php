<?php

namespace SoureCode\Component\Test\Tests\Fixtures\App;

use Nyholm\BundleTest\AppKernel;

class WorkaroundKernel extends AppKernel
{
    public function __construct(string $environment, bool $debug)
    {
        $cachePrefix = uniqid('cache', true);

        parent::__construct($cachePrefix);

        $this->environment = $environment;
        $this->debug = $debug;
    }

    public function handleOptions(array $options): void
    {
        if (array_key_exists('bundles', $options)) {
            foreach ($options['bundles'] as $bundle) {
                $this->addBundle($bundle);
            }
        }

        if (array_key_exists('configFiles', $options)) {
            foreach ($options['configFiles'] as $bundle) {
                $this->addConfigFile($bundle);
            }
        }

        if (array_key_exists('compilerPasses', $options)) {
            $this->addCompilerPasses($options['compilerPasses']);
        }

        if (array_key_exists('routingFile', $options)) {
            $this->setRoutingFile($options['routingFile']);
        }

        if (array_key_exists('projectDir', $options)) {
            $this->setProjectDir($options['projectDir']);
        }
    }
}
