<?php
return array(
    'modules' => array(
        'Application',
        'GithubFeed',
        'PhlyCommon',
        // 'PhlyBlog',
        'PhlyComic',
        'PhlyContact',
        // 'ZfcBase',
        // 'ZfcUser',
    ),
    'module_listener_options' => array( 
        'config_cache_enabled'     => false,
        'config_glob_paths'        => array(
            'config/autoload/{,*.}{global,local}.php',
            '/var/local/mwop.net/{,*.}{local}.php',
        ),
        'cache_dir'                => realpath(dirname(__DIR__) . '/data/cache'),
        'module_paths'             => array(
            realpath(__DIR__ . '/../module'),
            realpath(__DIR__ . '/../vendor'),
        ),
    ),
    'service_manager' => array(
        'use_defaults' => true,
        'factories'    => array(),
    ),
);
