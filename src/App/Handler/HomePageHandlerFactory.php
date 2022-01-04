<?php

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Mwop\Console\FeedAggregator;
use Psr\Container\ContainerInterface;

use function file_exists;
use function fwrite;
use function getcwd;
use function is_array;
use function realpath;
use function sprintf;

use const STDERR;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container): HomePageHandler
    {
        return new HomePageHandler(
            $this->getHomepagePosts(),
            $container->get(TemplateRendererInterface::class)
        );
    }

    private function getHomepagePosts(): array
    {
        $location = sprintf(FeedAggregator::CACHE_FILE, realpath(getcwd()));

        if (! file_exists($location)) {
            fwrite(STDERR, sprintf("Missing home page posts file at %s", $location));
            return [];
        }

        $posts = include $location;

        return is_array($posts)
            ? $posts
            : [];
    }
}
