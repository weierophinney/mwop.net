<?php

declare(strict_types=1);

namespace Mwop\Github;

use Illuminate\Support\Collection;
use Laminas\Escaper\Escaper;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Container\ContainerInterface;

class RenderLinksDelegator implements ExtensionInterface
{
    private const LINK_TEMPLATE = '<li><a href="%s">%s</a></li>';

    private readonly string $listLocation;

    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): Engine {
        $config             = $container->get('config-github');
        $this->listLocation = $config['list_file'];

        /** @var Engine $engine */
        $engine = $factory();
        $engine->loadExtension($this);
        return $engine;
    }

    public function register(Engine $engine): void
    {
        $engine->registerFunction('renderGithubFeed', [$this, 'renderLinks']);
    }

    public function renderLinks(): string
    {
        $escaper = new Escaper();
        $links   = (new Collection($this->parseList()))
            ->map(fn (array $item): AtomEntry => AtomEntry::fromArray($item))
            ->filter(fn (?AtomEntry $item): bool => $item !== null)
            ->map(fn (AtomEntry $item): string => (string) $item);

        return implode("\n", $links->toArray());
    }

    private function parseList(): array
    {
        if (! file_exists($this->listLocation)) {
            return [];
        }

        $json = file_get_contents($this->listLocation);

        try {
            $links = json_decode($json, true, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        return $links;
    }
}
