<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Factory;

use Mwop\Page;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class PageFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName) : Page
    {
        return new Page(
            $this->deriveTemplateName($requestedName),
            $container->get(TemplateRendererInterface::class)
        );
    }

    private function deriveTemplateName(string $service) : string
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

    private function camelCaseToDotSeparated(string $string) : string
    {
        return preg_replace_callback('/(^|[a-z])([A-Z])/', function ($matches) {
            $string = strlen($matches[1]) ? sprintf('%s.%s', $matches[1], $matches[2]) : $matches[2];
            return strtolower($string);
        }, $string);
    }
}
