<?php

namespace SoureCode\Component\Test\Tests;

use SoureCode\Component\Test\Tests\Fixtures\App\WorkaroundKernel;
use SoureCode\Component\Test\UrlTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlTraitTest extends KernelTestCase
{
    use UrlTrait;

    protected static function createKernel(array $options = [])
    {
        /**
         * @var WorkaroundKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->handleOptions($options);

        return $kernel;
    }

    /**
     * @dataProvider generateUrlDataProvider
     */
    public function testGenerateUrl(
        string $expected,
        string $name,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): void {
        // Act
        $url = $this->generateUrl($name, $parameters, $referenceType);

        // Assert
        self::assertSame($expected, $url);
    }

    public function generateUrlDataProvider(): array
    {
        return [
            ['/', 'app_home'],
            ['/blog', 'app_blog'],
            ['/blog/article', 'app_blog_article_index'],
            ['/blog/article/1', 'app_blog_article_show', ['id' => 1]],
            ['/blog/article/4', 'app_blog_article_show', ['id' => 4]],
            ['http://localhost/', 'app_home', [], UrlGeneratorInterface::ABSOLUTE_URL],
            ['http://localhost/blog', 'app_blog', [], UrlGeneratorInterface::ABSOLUTE_URL],
            ['http://localhost/blog/article', 'app_blog_article_index', [], UrlGeneratorInterface::ABSOLUTE_URL],
            ['http://localhost/blog/article/1', 'app_blog_article_show', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_URL],
            ['http://localhost/blog/article/4', 'app_blog_article_show', ['id' => 4], UrlGeneratorInterface::ABSOLUTE_URL],
        ];
    }

    protected function setUp(): void
    {
        static::$class = WorkaroundKernel::class;
        static::bootKernel([
            'routingFile' => __DIR__.'/Fixtures/App/config/routes.yml',
        ]);
    }

}
