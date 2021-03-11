<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\App;

use Laminas\Feed\Reader\Entry\EntryInterface;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use League\Plates\Engine;
use Mezzio\Application;
use Mezzio\Session\SessionMiddleware;
use Middlewares\Csp;
use Mwop\Blog\Handler\DisplayPostHandler;
use Phly\ConfigFactory\ConfigFactory;
use PhlyComic\Console\FetchAllComics;
use PhlyComic\Console\FetchComic;
use PhlyComic\Console\ListComics;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Swift_AWSTransport;

use function date;
use function getcwd;
use function preg_replace_callback;
use function realpath;
use function sprintf;
use function strpos;
use function strtotime;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'            => $this->getDependencies(),
            'cache'                   => $this->getCacheConfig(),
            'content-security-policy' => [],
            'homepage'                => $this->getHomePageConfig(),
            'mail'                    => $this->getMailConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Middleware\RedirectsMiddleware::class       => Middleware\RedirectsMiddleware::class,
                Middleware\XClacksOverheadMiddleware::class => Middleware\XClacksOverheadMiddleware::class,
                Middleware\XPoweredByMiddleware::class      => Middleware\XPoweredByMiddleware::class,
            ],
            // @codingStandardsIgnoreStart
            // phpcs:disable
            'factories' => [
                'config-cache'                               => ConfigFactory::class,
                'config-content-security-policy'             => ConfigFactory::class,
                'config-homepage'                            => ConfigFactory::class,
                'config-homepage.posts'                      => ConfigFactory::class,
                'config-instagram.feed'                      => ConfigFactory::class,
                'config-mail.transport'                      => ConfigFactory::class,
                Csp::class                                   => Middleware\ContentSecurityPolicyMiddlewareFactory::class,
                CacheItemPoolInterface::class                => Factory\CachePoolFactory::class,
                EventDispatcherInterface::class              => Factory\EventDispatcherFactory::class,
                FeedReaderHttpClientInterface::class         => Feed\HttpPlugClientFactory::class,
                Handler\ComicsPageHandler::class             => Handler\ComicsPageHandlerFactory::class,
                Handler\HomePageHandler::class               => Handler\HomePageHandlerFactory::class,
                Handler\PrivacyPolicyPageHandler::class      => Handler\PageHandlerFactory::class,
                Handler\ResumePageHandler::class             => Handler\PageHandlerFactory::class,
                'mail.transport'                             => Factory\MailTransport::class,
                Middleware\RedirectAmpPagesMiddleware::class => Middleware\RedirectAmpPagesMiddlewareFactory::class,
                SessionCachePool::class                      => SessionCachePoolFactory::class,
            ],
            // phpcs:enable
            // @codingStandardsIgnoreEnd
            'delegators' => [
                DisplayPostHandler::class => [
                    Middleware\DisplayBlogPostHandlerDelegator::class,
                ],
                Engine::class             => [
                    Factory\PlatesFunctionsDelegator::class,
                ],
            ],
        ];
    }

    public function getCacheConfig(): array
    {
        return [
            'connection-parameters' => [
                'scheme' => 'tcp',
                'host'   => 'localhost',
                'port'   => 6379,
            ],
        ];
    }

    public function getHomePageConfig(): array
    {
        return [
            'feed-count' => 10,
            'feeds'      => [
                [
                    'url'      => realpath(getcwd()) . '/data/feeds/rss.xml',
                    'sitename' => 'mwop.net',
                    'siteurl'  => 'https://mwop.net/blog',
                ],
                [
                    'url'         => 'https://www.zend.com/blog/feed',
                    'sitename'    => 'Zend',
                    'favicon'     => 'https://www.zend.com/sites/zend/themes/custom/zend/images/favicons/favicon.ico',
                    'siteurl'     => 'https://www.zend.com/blog',
                    'filters'     => [
                        function (EntryInterface $item): bool {
                            return false !== strpos($item->getAuthor()['name'], 'Phinney');
                        },
                    ],
                    'normalizers' => [
                        function (string $content): string {
                            // Munge publication dates to valid format (RSS)
                            return preg_replace_callback(
                                // phpcs:ignore Generic.Files.LineLength.TooLong
                                '#<pubDate>(?P<dow>Mon|Tue|Wed|Thu|Fri|Sat|Sun),\D+(?P<month>\d+)/(?P<day>\d+)/(?P<year>\d+)\D+(?P<hour>\d+):(?P<min>\d+)</pubDate>#',
                                function (array $matches): string {
                                    return sprintf(
                                        '<pubDate>%s, %02d %s %d %02d:%02d:00 America/Chicago</pubDate>',
                                        $matches['dow'],
                                        $matches['day'],
                                        // Need textual representation of Month name
                                        date('M', strtotime(sprintf(
                                            '%d-%02d-%02d',
                                            $matches['year'],
                                            $matches['month'],
                                            $matches['day']
                                        ))),
                                        $matches['year'],
                                        $matches['hour'],
                                        $matches['min'],
                                    );
                                },
                                $content
                            ) ?: $content;
                        },
                    ],
                ],
            ],
            'posts'      => [],
        ];
    }

    public function getMailConfig(): array
    {
        return [
            'transport' => [
                'class'    => Swift_AWSTransport::class,
                'username' => '',
                'password' => '',
            ],
        ];
    }

    public function registerRoutes(Application $app): void
    {
        $app->get('/', Handler\HomePageHandler::class, 'home');
        $app->get('/comics', Handler\ComicsPageHandler::class, 'comics');
        $app->get('/resume', Handler\ResumePageHandler::class, 'resume');
        $app->get('/privacy-policy', Handler\PrivacyPolicyPageHandler::class, 'privacy-policy');

        // Logout
        $app->get('/logout', [
            SessionMiddleware::class,
            Handler\LogoutHandler::class,
        ], 'logout');
    }
}
