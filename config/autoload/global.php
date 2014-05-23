<?php
return array(
    'phly-simple-page' => array(
        'cache' => array(
            'adapter' => array(
                'name' => 'filesystem',
                'options' => array(
                    'namespace'       => 'pages',
                    'cache_dir'       => getcwd() . '/data/pages',
                    'dir_permission'  => 0777,
                    'file_permission' => '0666',
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
