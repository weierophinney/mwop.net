<?php

declare(strict_types=1);

namespace Mwop\Feed;

use Laminas\Feed\Reader\Entry\EntryInterface;
use League\Plates\Engine;
use Mezzio\Application;
use Mezzio\ProblemDetails\ProblemDetailsMiddleware;
use Mezzio\Swoole\Task\DeferredServiceListenerDelegator;
use Mwop\Hooks\Middleware\ValidateWebhookRequestMiddleware;
use Phly\ConfigFactory\ConfigFactory;
use Phly\EventDispatcher\ListenerProvider\AttachableListenerProvider;

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
            'dependencies' => $this->getDependencies(),
            'laminas-cli'  => $this->getConsoleConfig(),
            'feeds'        => $this->getFeedsConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                AttachableListenerProvider::class => [
                    Webhook\PayloadListenerDelegator::class,
                ],
                Engine::class                     => [
                    HomepagePostsDelegator::class,
                ],
                Webhook\PayloadListener::class    => [
                    DeferredServiceListenerDelegator::class,
                ],
            ],
            'factories'  => [
                'config-feeds'                 => ConfigFactory::class,
                Console\FeedAggregator::class  => Console\FeedAggregatorFactory::class,
                Handler\RssHandler::class      => Handler\RssHandlerFactory::class,
                HomepagePostsList::class       => HomepagePostsListFactory::class,
                Webhook\PayloadListener::class => Webhook\PayloadListenerFactory::class,
            ],
        ];
    }

    public function getConsoleConfig(): array
    {
        return [
            'commands' => [
                'homepage-feeds' => Console\FeedAggregator::class,
            ],
        ];
    }

    public function getFeedsConfig(): array
    {
        return [
            'feed-count' => 10,
            'feeds'      => $this->getDefaultFeedsConfig(),
        ];
    }

    public function getDefaultFeedsConfig(): array
    {
        return [
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
                    // Munge publication dates to valid format (RSS)
                    fn (string $content): string  => preg_replace_callback(
                        // phpcs:ignore Generic.Files.LineLength.TooLong
                        '#<pubDate>(?P<dow>Mon|Tue|Wed|Thu|Fri|Sat|Sun),\D+(?P<month>\d+)/(?P<day>\d+)/(?P<year>\d+)\D+(?P<hour>\d+):(?P<min>\d+)</pubDate>#',
                        fn (array $matches): string => sprintf(
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
                        ),
                        $content
                    ) ?: $content,
                ],
            ],
        ];
    }

    public function registerRoutes(Application $app, string $basePath = ''): void
    {
        $app->post($basePath . '/api/feed/rss-item', [
            ProblemDetailsMiddleware::class,
            ValidateWebhookRequestMiddleware::class,
            Handler\RssHandler::class,
        ], 'api.hook.rss-item');
    }
}
