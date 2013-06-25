<?php
$configCache    = false;
$localCacheDir  = realpath(dirname(__DIR__) . '/data/cache');
return array(
    'modules' => array(
        'Application',
        'GithubFeed',
        'PhlyCommon',
        'PhlyBlog',
        'PhlyComic',
        'PhlyContact',
        'PhlySimplePage',
        'ScnSocialAuth',
        'ZfcBase',
        'ZfcUser',
    ),
    'module_listener_options' => array( 
        'config_cache_enabled'     => $configCache,
        'config_glob_paths'        => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'cache_dir'                => $localCacheDir,
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
