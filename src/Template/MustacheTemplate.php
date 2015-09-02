<?php
namespace Mwop\Template;

use Phly\Mustache\Mustache;
use Phly\Mustache\Resolver\AggregateResolver;
use Phly\Mustache\Resolver\DefaultResolver;
use SplStack;
use Zend\Expressive\Template\TemplateInterface;
use Zend\Expressive\Template\TemplatePath;

class MustacheTemplate implements TemplateInterface
{
    /**
     * @var Mustache
     */
    private $renderer;

    /**
     * @var AggregateResolver
     */
    private $resolver;

    /**
     * Constructor.
     *
     * Composes the Mustache instance. If the Mustache instance does not compose
     * an AggregateResolver, one is created, the original resolver is
     * attached to it, and the aggregate is pushed into the Mustache instance;
     * this is done to ensure namespaced template paths work correctly.
     */
    public function __construct(Mustache $renderer)
    {
        $this->renderer = $renderer;

        $resolver = $renderer->getResolver();

        if (! $resolver->hasType(DefaultResolver::class)) {
            $resolver->attach($this->createDefaultResolver(), 0);
        } else {
            $this->extractDefaultResolver($resolver);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render($name, $vars = [])
    {
        return $this->renderer->render($name, $vars);
    }

    /**
     * {@inheritDoc}
     */
    public function addPath($path, $namespace = null)
    {
        $this->resolver->addTemplatePath($path, $namespace);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaths()
    {
        $resolver = $this->resolver;
        $paths    = [];

        foreach ($resolver->getNamespaces() as $namespace) {
            $namespace = ($namespace !== DefaultResolver::DEFAULT_NAMESPACE) ? $namespace : null;
            foreach ($resolver->getTemplatePath($namespace) as $path) {
                $paths[] = new TemplatePath($path, $namespace);
            }
        }

        return $paths;
    }

    /**
     * Creates and returns a DefaultResolver.
     *
     * @return DefaultResolver
     */
    private function createDefaultResolver()
    {
        $this->resolver = new DefaultResolver();
        return $this->resolver;
    }

    /**
     * Extract and compose the DefaultResolver found in an AggregateResolver.
     *
     * Also sets the internal $resolver property to the first found.
     *
     * @param AggregateResolver $aggregate
     */
    private function extractDefaultResolver(AggregateResolver $aggregate)
    {
        if ($this->resolver instanceof DefaultResolver) {
            return;
        }

        $resolver = $aggregate->fetchByType(DefaultResolver::class);
        if ($resolver instanceof AggregateResolver) {
            $queue    = $resolver->getIterator();
            $resolver = $queue->top();
        }

        $this->resolver = $resolver;
    }
}
