<?php
$config = array();
$config['production'] = array(
    'bootstrap_class' => 'Application\Bootstrap',

    'disqus' => array(
        'key'         => 'phlyboyphly',
        'development' => 0,
    ),

    'di' => array( 'instance' => array(
        'alias' => array(
            'view'  => 'Zend\View\PhpRenderer',
        ),

        'Zend\View\HelperLoader' => array('parameters' => array(
            'map' => array(
                'url'    => 'Application\View\Helper\Url',
                'disqus' => 'Application\View\Helper\Disqus',
            ),
        )),

        'Zend\View\HelperBroker' => array('parameters' => array(
            'loader' => 'Zend\View\HelperLoader',
        )),

        'Zend\View\PhpRenderer' => array(
            'methods' => array(
                'setResolver' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'application' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
            'parameters' => array(
                'broker' => 'Zend\View\HelperBroker',
            ),
        ),
    )),

    'routes' => array(
        'home' => array(
            'type'    => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/',
                'defaults' => array(
                    'controller' => 'Application\Controller\PageController',
                    'page'       => 'home',
                ),
            ),
        ),
        'comics' => array(
            'type'    => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/comics',
                'defaults' => array(
                    'controller' => 'Application\Controller\PageController',
                    'page'       => 'comics',
                ),
            ),
        ),
        'resume' => array(
            'type'    => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/resume',
                'defaults' => array(
                    'controller' => 'Application\Controller\PageController',
                    'page'       => 'resume',
                ),
            ),
        ),
    ),
);
$config['staging']     = $config['production'];
$config['testing']     = $config['production'];
$config['development'] = $config['production'];
$config['development']['disqus']['key']         = "testphlyboyphly";
$config['development']['disqus']['development'] = 1;
return $config;
