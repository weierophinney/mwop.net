<?php
namespace Mwop\Template;

use Phly\Mustache\Mustache;

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
}
