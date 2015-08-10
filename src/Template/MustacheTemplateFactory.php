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
        $mustache->setTemplatePath(getcwd() . '/templates');
        $mustache->setTemplatePath(getcwd() . '/data');

        return new MustacheTemplate($mustache);
    }
}
