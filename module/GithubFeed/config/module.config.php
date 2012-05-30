<?php
return array(
    'github_feed' => array(
        'user'  => 'github username here',
        'token' => 'github API token here',
        'limit' => 5,
    ),
    'service_manager' => array(
        'factories' => array(
            'GithubFeed\AtomReader' => function ($services) {
                $config = $services->get('config');
                $config = $config['github_feed'];
                $reader = new GithubFeed\AtomReader($config['user'], $config['token']);
                if (isset($config['limit'])) {
                    $reader->setLimit($config['limit']);
                }
                return $reader;
            },
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'github-feed/links' => __DIR__ . '/../view/github-feed/links.phtml',
        ),
        'template_path_stack' => array(
            'github-feed' => __DIR__ . '/../view',
        ),
    ),
);
