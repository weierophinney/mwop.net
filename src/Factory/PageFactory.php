<?php
namespace Mwop\Factory;

use Mwop\Page;
use Mwop\PageView;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageFactory
{
    public function __invoke($services, $canonicalName, $requestedName)
    {
        $view = new PageView();
        $view->setRouter($services->get(RouterInterface::class));

        return new Page(
            $this->deriveTemplateName($requestedName),
            $view,
            $services->get(TemplateRendererInterface::class)
        );
    }

    private function deriveTemplateName($service)
    {
        // Separate the first namespace out as the template namespace
        $ns       = preg_quote('\\');
        $pattern  = sprintf('#^([^%s]+)%s+(.*)$#', $ns, $ns);
        $template = preg_replace($pattern, '$1::$2', $service);

        if (strstr($template, '::')) {
            list($namespace, $template) = explode('::', $template, 2);
            return sprintf(
                '%s::%s',
                $this->camelCaseToDotSeparated($namespace),
                $this->camelCaseToDotSeparated($template)
            );
        }

        return $this->camelCaseToDotSeparated($template);
    }

    private function camelCaseToDotSeparated($string)
    {
        return preg_replace_callback('/(^|[a-z])([A-Z])/', function ($matches) {
            $string = strlen($matches[1]) ? sprintf('%s.%s', $matches[1], $matches[2]) : $matches[2];
            return strtolower($string);
        }, $string);
    }
}
