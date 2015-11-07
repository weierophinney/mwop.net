<?php
namespace Mwop\Job;

use Zend\Expressive\AppFactory;

class MiddlewareFactory
{
    public function __invoke($services)
    {
        $jobs = AppFactory::create($services);

        $jobs->post('/clear-cache', ClearCache::class);
        $jobs->post('/comics', Comics::class);
        $jobs->post('/github-feed', GithubFeed::class);

        return $jobs;
    }
}
