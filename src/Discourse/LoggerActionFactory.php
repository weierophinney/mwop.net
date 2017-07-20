<?php

namespace Mwop\Discourse;

use Psr\Container\ContainerInterface;

class LoggerActionFactory
{
    public function __invoke(ContainerInterface $container) : LoggerAction
    {
        return new LoggerAction($container->get(__NAMESPACE__ . '\Logger'));
    }
}
