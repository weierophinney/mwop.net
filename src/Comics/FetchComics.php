<?php

declare(strict_types=1);

namespace Mwop\Comics;

use PhlyComic\Comic;
use PhlyComic\ComicFactory;
use Psr\Log\LoggerInterface;
use Throwable;

use function array_filter;
use function array_keys;
use function file_put_contents;
use function in_array;
use function ksort;
use function sprintf;

class FetchComics
{
    private const TEMPLATE_COMIC = <<<'EOT'
        <div class="comic">
            <h4><a href="%s">%s</a></h4>
            <p><a href="%s"><img src="%s"/></a></p>
        </div>

        EOT;

    private const TEMPLATE_ERROR = <<<'EOT'
        <div class="comic">
            <h4><a href="%s">%s</a></h4>
            <p class="error">%s</p>
        </div>

        EOT;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly array $exclusions,
        private readonly string $comicsFile,
    ) {
    }

    public function __invoke(ComicsEvent $event): void
    {
        $supported = ComicFactory::getSupported();
        ksort($supported);

        $toFetch = array_filter(
            array_keys($supported),
            fn (string $comic): bool => ! in_array($comic, $this->exclusions, true),
        );

        $html = $this->fetchComics($toFetch);
        file_put_contents($this->comicsFile, $html);

        $this->logger->info(sprintf('Comics written to %s', $this->comicsFile));
    }

    private function fetchComics(array $comics): string
    {
        $html = '';
        foreach ($comics as $name) {
            $this->logger->info(sprintf('Attempting to fetch comic "%s"', $name));

            try {
                $comic = $this->fetchComic($name);
            } catch (Throwable $e) {
                $this->logger->info(sprintf(
                    'EXCEPTION fetching comic "%s" (%s): %s',
                    $name,
                    $e::class,
                    $e->getMessage()
                ));
                continue;
            }

            if (! $comic instanceof Comic) {
                $this->logger->info(sprintf('FAILED to fetch comic "%s"', $name));
                continue;
            }

            $this->logger->info(sprintf('SUCCEEDED fetching comic "%s"', $name));
            $html .= $this->createComicOutput($comic);
        }
        return $html;
    }

    private function fetchComic(string $name): ?Comic
    {
        $source = ComicFactory::factory($name);
        $comic  = $source->fetch();

        if (! $comic instanceof Comic) {
            $this->logger->error(sprintf(
                'Error fetching comic "%s": %s',
                $name,
                $source->getError(),
            ));

            return null;
        }

        return $comic;
    }

    private function createComicOutput(Comic $comic): string
    {
        if ($comic->hasError()) {
            return sprintf(
                self::TEMPLATE_ERROR,
                $comic->getLink(),
                $comic->getName(),
                $comic->getError()
            );
        }

        return sprintf(
            self::TEMPLATE_COMIC,
            $comic->getLink(),
            $comic->getName(),
            $comic->getDaily(),
            $comic->getImage()
        );
    }
}
