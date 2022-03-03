<?php

declare(strict_types=1);

namespace Mwop\Now;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Container\ContainerInterface;

use function preg_replace;

class MarkdownPlatesDelegator implements ExtensionInterface
{
    private MarkdownConverter $markdown;

    public function __construct()
    {
        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        $this->markdown = new MarkdownConverter($environment);
    }

    public function __invoke(
        ContainerInterface $container,
        string $servicename,
        callable $factory,
    ): Engine {
        /** @var Engine $engine */
        $engine = $factory();
        $engine->loadExtension($this);

        return $engine;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('markdown', [$this, 'convertMarkdownToHtml']);
    }

    public function convertMarkdownToHtml(string $markdown): string
    {
        $markdown = preg_replace(
            '/^\#/m',
            '###',
            $markdown
        );

        return $this->markdown->convert($markdown)->getContent();
    }
}
