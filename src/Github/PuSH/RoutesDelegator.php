<?php

namespace Mwop\Github\PuSH;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;

class RoutesDelegator
{
    private $events = [
        'issues',
        'issue_comment',
        'pull_request',
        'pull_request_review',
        'pull_request_review_comment',
        'release',
        'status',
    ];

    public function __invoke(ContainerInterface $container, string $name, callable $callback) : Application
    {
        $app = $callback();
        foreach ($this->events as $event) {
            $app->post(
                sprintf('/github/{room}/%s', $event),
                LoggerAction::class,
                sprintf('github.%s', $event)
            );
        }
        return $app;
    }
}
