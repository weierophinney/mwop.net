<?php
namespace Mwop\Template;

use Phly\Mustache\Mustache;
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

    public function __construct(Mustache $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function render($name, $vars = [])
    {
        return $this->renderer->render($name, $vars);
    }

    public function addPath($path, $namespace = null)
    {
        $resolver = $this->renderer->getResolver();
        if (! $resolver instanceof DefaultResolver) {
            return;
        }
        $resolver->setTemplatePath($path);
    }

    public function getPaths()
    {
        $resolver = $this->renderer->getResolver();
        if (! $resolver instanceof DefaultResolver) {
            return;
        }

        $paths = [];
        foreach ($resolver->getTemplatePath() as $path) {
            $paths[] = new TemplatePath($path);
        }
        return $paths;
    }
}
