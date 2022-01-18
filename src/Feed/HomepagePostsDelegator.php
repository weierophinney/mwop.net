<?php

declare(strict_types=1);

namespace Mwop\Feed;

use JsonException;
use Laminas\Escaper\Escaper;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function array_map;
use function file_exists;
use function file_get_contents;
use function getcwd;
use function implode;
use function json_decode;
use function realpath;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class HomepagePostsDelegator implements ExtensionInterface
{
    private const TEMPLATE_POST = <<<'END'
        <li><a href="%s"><img src="%s" alt="%s" title="%s" width="16"></a>&nbsp;<a href="%s">%s</a></li>
        END;

    private ?LoggerInterface $logger;

    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $factory
    ): Engine {
        $this->logger = $container->get(LoggerInterface::class);

        /** @var Engine $engine */
        $engine = $factory();
        $engine->loadExtension($this);

        return $engine;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('homepagePosts', [$this, 'homepagePosts']);
    }

    public function homepagePosts(): string
    {
        $cacheFile = sprintf(Console\FeedAggregator::CACHE_FILE, realpath(getcwd()));
        if (! file_exists($cacheFile)) {
            return '';
        }

        $json = file_get_contents($cacheFile);
        try {
            $items = json_decode($json, true, 4, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->logger?->warning(sprintf(
                "Error parsing feed cache file: %s\nContents:\n%s",
                $e->getMessage(),
                $json,
            ));
            return '';
        }

        $escaper = new Escaper('utf-8');

        return implode("\n", array_map(
            fn (array $item): string => sprintf(
                self::TEMPLATE_POST,
                $item['siteurl'],
                $item['favicon'],
                $escaper->escapeHtmlAttr($item['sitename']),
                $escaper->escapeHtmlAttr($item['sitename']),
                $item['link'],
                $escaper->escapeHtml($item['title']),
            ),
            $items,
        ));
    }
}
