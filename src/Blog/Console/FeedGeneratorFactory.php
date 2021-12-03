<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found


declare(strict_types=1);

namespace Mwop\Blog\Console;

use Mezzio\Helper\ServerUrlHelper;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

use function getcwd;
use function realpath;

class FeedGeneratorFactory
{
    public function __invoke(ContainerInterface $container): FeedGenerator
    {
        return new FeedGenerator(
            mapper: $container->get(MapperInterface::class),
            router: $container->get(RouterInterface::class),
            renderer: $container->get(TemplateRendererInterface::class),
            serverUrlHelper: $container->get(ServerUrlHelper::class),
            authorsPath: realpath(getcwd()) . '/data/blog/authors/',
        );
    }
}
