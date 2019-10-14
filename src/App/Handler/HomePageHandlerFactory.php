<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Handler;

use Mwop\Console\FeedAggregator;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageHandlerFactory
{
    public function __invoke(ContainerInterface $container) : HomePageHandler
    {
        $config = $container->get('config');
        return new HomePageHandler(
            $this->getHomepagePosts(),
            $config['instagram']['feed'] ?? '',
            $container->get(TemplateRendererInterface::class)
        );
    }

    private function getHomepagePosts() : array
    {
        $location = sprintf(FeedAggregator::CACHE_FILE, realpath(getcwd()));

        if (! file_exists($location)) {
            fwrite(STDERR, sprintf("Missing home page posts file at %s", $location));
            return [];
        }

        $posts = include $location;

        if (! is_array($posts)) {
            return [];
        }

        return $posts;
    }
}
