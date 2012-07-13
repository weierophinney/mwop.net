<?php
return array(
    'github_feed' => array(
        'user'  => 'github username here',
        'token' => 'github API token here',
        'limit' => 5,
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
