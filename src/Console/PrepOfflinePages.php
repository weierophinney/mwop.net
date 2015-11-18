<?php
namespace Mwop\Console;

use Herrera\Version;
use Mwop\Blog\MapperInterface;
use Zend\Expressive\Router\RouterInterface;

class PrepOfflinePages
{
    const MARKER_START    = '/* @generator-marker@ */';
    const MARKER_END      = '/* @generator-marker@ */';
    const VERSION_REGEX   = "/^var version \= 'v(?P<version>[^:']+):';/s";
    const VERSION_REPLACE = "/^(var version \= 'v)[^:']+(:';)/s";

    /**
     * @var array Default paths to always include in the service-worker
     */
    private $defaultPaths = [
        '/',
        '/offline',
        '/resume',
    ];

    private $mapper;

    private $router;

    public function __construct(MapperInterface $mapper, RouterInterface $router)
    {
        $this->mapper = $mapper;
        $this->router = $router;
    }

    public function __invoke($route, $console)
    {
        $serviceWorker = $route->getMatchedParam('serviceWorker');

        $this->console->writeLine('Updating service worker default offline pages');

        $paths = $this->defaultPaths;
        foreach ($this->generatePaths() as $path) {
            $paths[] = $path;
        }

        $this->updateServiceWorker($serviceWorker, $paths);

        $this->console->writeLine('[DONE]');
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
            yield $this->generateUri('blog.post', $post['id']);
        }
    }

    /**
     * Normalize generated URIs.
     *
     * @param string $route
     * @param string $id
     * @return string
     */
    private function generateUri($route, $id)
    {
        $uri = $this->router->generateUri($route, ['id' => $id]);
        return str_replace('[/]', '', $uri);
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
        $contents = $this->replaceOfflinePaths(json_encode($paths), $contents);
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
            return $serviceWorker;
        }

        $version = $matches['version'];
        $builder = Version\Parser::toBuilder($version);
        $builder->incrementPatch();
        $version = Version\Dumper::toString($builder->getVersion());

        return preg_replace(self::VERSION_REPLACE, '$1' . $version . '$2', $serviceWorker);
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
        $pattern  = sprintf(
            "/^(%s[\r\n]+).*?([\r\n]+%s)$/s",
            preg_quote(self::MARKER_START),
            preg_quote(self::MARKER_END)
        );
        $replacement = sprintf(
            "%s\nvar offline = %s;\n%s",
            self::MARKER_START,
            $paths,
            self::MARKER_END
        );

        return preg_replace($pattern, $replacement, $serviceWorker);
    }
}
