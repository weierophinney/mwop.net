<?php
namespace Mwop\Template;

use Phly\Mustache\Mustache;
use Phly\Mustache\Resolver\ResolverInterface;
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
     * @var AggregateMustacheResolver
     */
    private $resolver;

    /**
     * Constructor.
     *
     * Composes the Mustache instance. If the Mustache instance does not compose
     * an AggregateMustacheResolver, one is created, the original resolver is
     * attached to it, and the aggregate is pushed into the Mustache instance;
     * this is done to ensure namespaced template paths work correctly.
     */
    public function __construct(Mustache $renderer)
    {
        $this->renderer = $renderer;

        $resolver = $renderer->getResolver();

        if (! $resolver instanceof AggregateMustacheResolver) {
            $aggregate = $this->createDefaultResolver();
            $aggregate->attach($resolver);
            $renderer->setResolver($aggregate);
            $resolver = $aggregate;
        }

        if (! $this->aggregateHasNamespacedResolver($resolver)) {
            $this->injectNamespacedResolver($resolver);
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
        $this->resolver->setTemplatePath($path, $namespace);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaths()
    {
        $resolver = $this->resolver;
        $paths    = [];

        foreach ($resolver->getNamespaces() as $namespace) {
            $namespace = ($namespace !== NamespacedMustacheResolver::DEFAULT_NAMESPACE) ? $namespace : null;
            foreach ($resolver->getTemplatePath($namespace) as $path) {
                $paths[] = new TemplatePath($path, $namespace);
            }
        }

        return $paths;
    }

    /**
     * Creates and returns an AggregateMustacheResolver composing a NamespacedMustacheResolver.
     *
     * NamespacedMustacheResolver is set at -1 priority, to ensure it resolves
     * later than any composed originally.
     *
     * @return AggregateMustacheResolver
     */
    private function createDefaultResolver()
    {
        $resolver = new AggregateMustacheResolver();
        $this->injectNamespacedResolver($resolver);
        return $resolver;
    }

    /**
     * Does the aggregate compose a namespaced resolver?
     *
     * If it does, it sets the internal $resolver property to the first found.
     *
     * If the internal $resolver property is already set, returns early.
     *
     * @param AggregateMustacheResolver $aggregate
     * @return bool
     */
    private function aggregateHasNamespacedResolver(AggregateMustacheResolver $aggregate)
    {
        if ($this->resolver) {
            return true;
        }

        foreach ($aggregate as $resolver) {
            if ($resolver instanceof NamespacedMustacheResolver) {
                $this->resolver = $resolver;
                return true;
            }
        }

        return false;
    }

    /**
     * Inject the aggregate with a namespaced resolver.
     *
     * Also sets the internal $resolver property to the first found.
     *
     * @param AggregateMustacheResolver $aggregate
     */
    private function injectNamespacedResolver(AggregateMustacheResolver $aggregate)
    {
        $this->resolver = new NamespacedMustacheResolver();
        $aggregate->attach($this->resolver, -1);
    }
}
