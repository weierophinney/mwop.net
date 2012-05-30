<?php
return array(
    'authentication' => array(
        'Application\Controller\PageController' => array(
            'comics',
        ),
    ),

    'view' => array(
        'dojo-config' => 'dojo-config.phtml',
        'search'      => array(
            'api_key' => 'GOOGLE_SEARCH_KEY_GOES_HERE',
        )
    ),

    'disqus' => array(
    ),

    'view_manager' => array(
        'doctype' => 'HTML5',
        'layout'  => 'layout',
        'template_map' => array(
            'about'        => __DIR__ . '/../view/about.phtml',
            'analytics'    => __DIR__ . '/../view/analytics.phtml',
            'error'        => __DIR__ . '/../view/error.phtml',
            'layout'       => __DIR__ . '/../view/layout.phtml',
            'projects'     => __DIR__ . '/../view/projects.phtml',
            'searchbox'    => __DIR__ . '/../view/searchbox.phtml',
            'search'       => __DIR__ . '/../view/search.phtml',
            'pages/404'    => __DIR__ . '/../view/pages/404.phtml',
            'pages/comics' => __DIR__ . '/../view/pages/comics.phtml',
            'pages/home'   => __DIR__ . '/../view/pages/home.phtml',
            'pages/resume' => __DIR__ . '/../view/pages/resume.phtml',
        ),
        'template_path_stack' => array(
            'application' => __DIR__ . '/../view',
        ),
        'helper_map' => array(
            'disqus' => 'Application\View\Helper\Disqus',
        ),
        'display_exceptions' => true,
        'exception_template' => 'error',
        'not_found_template' => 'pages/404',
    ),

    'service_manager' => array(
        'factories' => array(
            'Application\View\Helper\Disqus' => function ($services) {
                $config = $services->get('config');
                if ($config instanceof Zend\Config\Config) {
                    $config = $config->toArray();
                }
                $config = $config['disqus'];
                return new Application\View\Helper\Disqus($config);
            },
        ),
    ),

    'controller' => array(
        'class' => array(
            'application-page'                      => 'Application\Controller\PageController',
            'Application\Controller\PageController' => 'Application\Controller\PageController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'default' => array(
                'type' => 'Regex',
                'options' => array(
                    'regex' => '/.*',
                    'defaults' => array(
                        'controller' => 'Application\Controller\PageController',
                        'action'     => '404',
                    ),
                    'spec' => '404',
                ),
            ),
            'home' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\PageController',
                        'action'     => 'home',
                    ),
                ),
            ),
            'comics' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route' => '/comics',
                    'defaults' => array(
                        'controller' => 'Application\Controller\PageController',
                        'action'     => 'comics',
                    ),
                ),
            ),
            'resume' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route' => '/resume',
                    'defaults' => array(
                        'controller' => 'Application\Controller\PageController',
                        'action'     => 'resume',
                    ),
                ),
            ),
        ),
    ),
);
