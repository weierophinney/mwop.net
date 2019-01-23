<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\App\Handler;

use Mwop\OAuth2\Middleware\CheckAuthenticationMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Expressive\MiddlewareFactory;
use Zend\Expressive\Session\SessionMiddleware;

class ComicsPageHandlerAuthDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ) : MiddlewareInterface {
        $factory = $container->get(MiddlewareFactory::class);
        return $factory->pipeline(
            $container->get(SessionMiddleware::class),
            $container->get(CheckAuthenticationMiddleware::class),
            $callback()
        );
    }
}
