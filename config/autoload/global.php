<?php
return array(
    'phly-comic' => array(
        'output_path'     => 'data/',
        'comic_file'      => '%s.html',
        'all_comics_file' => 'comics.html',
    ),
    'phly-simple-page' => array(
        'cache' => array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'namespace' => 'pages',
                    'cache_dir' => getcwd() . '/data/pages',
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ),
        'factories' => array(
            'PhlySimplePage\PageCache' => 'PhlySimplePage\PageCacheService',
        ),
    ),
);
