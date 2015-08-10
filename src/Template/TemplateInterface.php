<?php
namespace Mwop\Template;

interface TemplateInterface
{
    /**
     * Render the named template, passing it the given variables.
     *
     * @param string $name
     * @param array|object $vars
     * @return string
     */
    public function render($name, $vars = []);
}
