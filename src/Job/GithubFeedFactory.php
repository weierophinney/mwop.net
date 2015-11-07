<?php
namespace Mwop\Job;

class GithubFeedFactory
{
    public function __invoke($services)
    {
        return new GithubFeed($services->get('Mwop\Github\AtomReader'));
    }
}
