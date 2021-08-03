<?php

use SoureCode\Component\Test\Tests\Fixtures\FooBundle\Repository\BookRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set(BookRepository::class)
        ->tag('doctrine.repository_service')
        ->public()
        ->args(
            [
                service('doctrine'),
            ]
        );
};
