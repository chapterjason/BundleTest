<?php

namespace SoureCode\Component\Test;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UrlTrait
{
    public function generateUrl(
        string $name,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $container = self::getContainer();

        /**
         * @var UrlGeneratorInterface $urlGenerator
         */
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        return $urlGenerator->generate($name, $parameters, $referenceType);
    }
}
