<?php
namespace Mwop\Console;

use Herrera\Version;
use Mwop\Blog\MapperInterface;
use Zend\Expressive\Router\RouterInterface;

class PrepOfflinePages
{
    const OFFLINE_REGEX   = "/\nvar offline \= \[.*?\];/s";
    const VERSION_REGEX   = "/^var version \= 'v(?P<version>[^:']+)\:';/m";

    /**
     * @var array Default paths to always include in the service-worker
     */
    private $defaultPaths = [
        // Pages
        '/',
        '/blog',
        '/offline',
        '/resume',

        // Assets
        '/css/site.min.css',
        '/images/favicon/apple-touch-icon-57x57.png',
        '/images/favicon/apple-touch-icon-60x60.png',
        '/images/favicon/apple-touch-icon-72x72.png',
        '/images/favicon/favicon-32x32.png',
        '/images/favicon/favicon-16x16.png',
        '/images/logo.gif',
        '/images/mwop-coffee-dpc09.jpg',
        '/manifest.json',
        '/js/bootstrap.min.js',
        'https://www.google.com/jsapi?ABQIAAAAGybdRRvLZwVUcF0dE3oVdBTO-MlgA7VGJpGqyqTOeDXlNzyZQxTGq17s-iAB0m0vwqLQ_A2dHhTg2Q',
        'https://code.jquery.com/jquery-1.10.2.min.js',
    ];

    private $mapper;

    public function __construct(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke($route, $console)
    {
        $serviceWorker = $route->getMatchedParam('serviceWorker');

        $console->writeLine('Updating service worker default offline pages');

        $paths = $this->defaultPaths;
        foreach ($this->generatePaths() as $path) {
            $paths[] = $path;
        }

        $this->updateServiceWorker($serviceWorker, $paths);

        $console->writeLine('[DONE]');
    }

    /**
     * Generator: first page of blog post URIs
     *
     * @return string[]
     */
    private function generatePaths()
    {
        $posts = $this->mapper->fetchAll();
        $posts->setCurrentPageNumber(1);

        foreach ($posts as $post) {
            yield sprintf('/blog/%s.html', $post['id']);
        }
    }

    /**
     * Update the service worker script contents
     *
     * @param string $serviceWorker Path to the service-worker.js script
     * @param array $paths Default offline paths
     */
    private function updateServiceWorker($serviceWorker, array $paths)
    {
        if (! file_exists($serviceWorker)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid service-worker path (%s); please provide a valid path to the service-worker',
                $serviceWorker
            ));
        }

        $contents = file_get_contents($serviceWorker);
        $contents = $this->bumpServiceWorkerVersion($contents);
        $contents = $this->replaceOfflinePaths(
            json_encode($paths, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            $contents
        );
        file_put_contents($serviceWorker, $contents);
    }

    /**
     * Bump the service-worker patch version
     *
     * @param string $serviceWorker
     * @return string
     */
    private function bumpServiceWorkerVersion($serviceWorker)
    {
        if (! preg_match(self::VERSION_REGEX, $serviceWorker, $matches)) {
            printf("Did not match version regex!\n");
            printf("    Version regex: %s\n", self::VERSION_REGEX);
            return $serviceWorker;
        }

        $replacement = sprintf(
            'var version = \'v%s:\';',
            $this->incrementVersion($matches['version'])
        );

        return preg_replace(self::VERSION_REGEX, $replacement, $serviceWorker);
    }

    /**
     * Increment the patch version
     *
     * @param string $version
     * @return string
     */
    private function incrementVersion($version)
    {
        $builder = Version\Parser::toBuilder($version);
        $builder->incrementPatch();
        return Version\Dumper::toString($builder->getVersion());
    }

    /**
     * Replace the offline paths variable contents in the service-worker.js
     *
     * @param string $paths JSON-encoded array of offline paths
     * @param string $serviceWorker Contents of the service-worker.js file
     * @return string
     */
    private function replaceOfflinePaths($paths, $serviceWorker)
    {
        $replacement = sprintf(
            "\nvar offline = %s;",
            $paths
        );

        if (! preg_match(self::OFFLINE_REGEX, $serviceWorker)) {
            printf("Did not match offline-path regex!\n");
            printf("    Pattern: %s\n", self::OFFLINE_REGEX);
            return $serviceWorker;
        }

        return preg_replace(self::OFFLINE_REGEX, $replacement, $serviceWorker);
    }
}
