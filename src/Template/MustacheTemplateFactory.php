<?php
namespace Mwop\Template;

use Phly\Mustache\Mustache;
use Phly\Mustache\Pragma\ImplicitIterator;

class MustacheTemplateFactory
{
    public function __invoke($services)
    {
        $mustache = new Mustache();
        $mustache->getRenderer()->addPragma(new ImplicitIterator());

        $templates = new MustacheTemplate($mustache);

        $templates->addPath(getcwd() . '/templates/blog', 'blog');
        $templates->addPath(getcwd() . '/templates/contact', 'contact');
        $templates->addPath(getcwd() . '/templates/error', 'error');
        $templates->addPath(getcwd() . '/templates/layout', 'layout');
        $templates->addPath(getcwd() . '/templates/mwop', 'mwop');

        $templates->addPath(getcwd() . '/templates');
        $templates->addPath(getcwd() . '/data');

        return new MustacheTemplate($mustache);
    }
}
