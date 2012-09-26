<?php
return array(
    'github_feed' => array(
        'user'         => 'github username here',
        'token'        => 'github API token here',
        'limit'        => 5,
        'content_path' => 'data/github-feed-links.phtml',
    ),
    'view_manager' => array(
        'template_map' => array(
            'github-feed/links' => __DIR__ . '/../view/github-feed/links.phtml',
        ),
        'template_path_stack' => array(
            'github-feed' => __DIR__ . '/../view',
        ),
    ),
    'console' => array('router' => array('routes' => array(
        'github-feed-fetch' => array(
            'type' => 'Simple',
            'options' => array(
                'route' => 'githubfeed fetch',
                'defaults' => array(
                    'controller' => 'GithubFeed\Fetch',
                    'action'     => 'feed',
                ),
            ),
        ),
    ))),
);
