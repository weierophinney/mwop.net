<?php

declare(strict_types=1);

namespace Mwop\Github;

use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\MapperBuilder;
use Illuminate\Support\Collection;
use JsonException;
use Laminas\Escaper\Escaper;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Psr\Container\ContainerInterface;
use Webmozart\Assert\Assert;

use function file_exists;
use function file_get_contents;
use function implode;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class RenderLinksDelegator implements ExtensionInterface
{
    private readonly string $listLocation;
    private readonly TreeMapper $dataMapper;

    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $factory,
    ): Engine {
        $config             = $container->get('config-github');
        $this->listLocation = $config['list_file'];

        /** @var MapperBuilder $builder */
        $builder = $container->get(MapperBuilder::class);
        Assert::isInstanceOf($builder, MapperBuilder::class);
        $this->dataMapper = $builder->mapper();

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
            ->map(fn (array $item): AtomEntry => $this->dataMapper->map(AtomEntry::class, Source::array($item)))
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
