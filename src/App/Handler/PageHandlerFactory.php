<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageHandlerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName) : PageHandler
    {
        return new PageHandler(
            $this->deriveTemplateName($requestedName),
            $container->get(TemplateRendererInterface::class)
        );
    }

    private function deriveTemplateName(string $service) : string
    {
        $parts = explode('\\', $service);
        $ns    = array_shift($parts);
        $page  = array_pop($parts);
        $page  = preg_replace('/Handler$/', '', $page);

        return sprintf(
            '%s::%s',
            $this->camelCaseToDotSeparated($ns),
            $this->camelCaseToDotSeparated($page)
        );
    }

    private function camelCaseToDotSeparated(string $string) : string
    {
        return preg_replace_callback('/(^|[a-z])([A-Z])/', function ($matches) {
            $string = strlen($matches[1]) ? sprintf('%s.%s', $matches[1], $matches[2]) : $matches[2];
            return strtolower($string);
        }, $string);
    }
}
