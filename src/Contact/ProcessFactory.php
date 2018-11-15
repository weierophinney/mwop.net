<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Contact;

use Psr\Container\ContainerInterface;
use Swoole\Http\Server as HttpServer;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Template\TemplateRendererInterface;

class ProcessFactory
{
    public function __invoke(ContainerInterface $container) : Process
    {
        return new Process(
            $container->get(HttpServer::class),
            $container->get(TemplateRendererInterface::class),
            $container->get(UrlHelper::class),
            $container->get('config')['contact']
        );
    }
}
