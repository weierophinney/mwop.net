<?php // phpcs:disable Generic.PHP.DiscourageGoto.Found

declare(strict_types=1);

namespace Mwop\Blog\Handler;

use Mezzio\Helper\UrlHelper;
use Mwop\Blog\Mapper\MapperInterface;
use Psr\Container\ContainerInterface;

class SearchHandlerFactory
{
    public function __invoke(ContainerInterface $container): SearchHandler
    {
        return new SearchHandler(
            mapper: $container->get(MapperInterface::class),
            urlHelper: $container->get(UrlHelper::class),
        );
    }
}
