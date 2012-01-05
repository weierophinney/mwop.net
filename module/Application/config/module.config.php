<?php
$config = array();
$config = array(
    'authentication' => array(
        'Application\Controller\PageController' => array(
            'comics',
        ),
    ),

    'view' => array(
        'layout' => 'layout.phtml',
        'search' => array(
            'api_key' => 'GOOGLE_SEARCH_KEY_GOES_HERE',
        )
    ),

    'di' => array( 
    'definition' => array('class' => array(
        'Application\View\Helper\Disqus' => array(
            'setOptions' => array(
                'options' => array(
                    'required' => true,
                    'type'     => 'array',
                ),
            ),
        ),
    )),
    'instance' => array(
        'alias' => array(
            'view'             => 'Zend\View\PhpRenderer',
            'view-resolver'    => 'Zend\View\TemplatePathStack',
            'view-broker'      => 'Zend\View\HelperBroker',
            'view-loader'      => 'Zend\View\HelperLoader',
            'application-page' => 'Application\Controller\PageController',
        ),

        'view-loader' => array('parameters' => array(
            'map' => array(
                'url'    => 'Application\View\Helper\Url',
                'disqus' => 'Application\View\Helper\Disqus',
            ),
        )),

        'view-broker' => array('parameters' => array(
            'loader' => 'view-loader',
        )),

        'view' => array( 'parameters' => array(
            'resolver' => 'view-resolver',
            'broker'   => 'view-broker',
        )),

        'view-resolver' => array('parameters' => array(
            'paths' => array(
                'application' => __DIR__ . '/../view',
            ),
        )),
        'Application\View\Helper\Disqus' => array('parameters' => array(
            'options' => array(),
        )),
    )),

    'routes' => array(
        'default' => array(
            'type' => 'Regex',
            'options' => array(
                'regex' => '/.*',
                'defaults' => array(
                    'controller' => 'application-page',
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
                    'controller' => 'application-page',
                    'action'     => 'home',
                ),
            ),
        ),
        'comics' => array(
            'type'    => 'Literal',
            'options' => array(
                'route' => '/comics',
                'defaults' => array(
                    'controller' => 'application-page',
                    'action'     => 'comics',
                ),
            ),
        ),
        'resume' => array(
            'type'    => 'Literal',
            'options' => array(
                'route' => '/resume',
                'defaults' => array(
                    'controller' => 'application-page',
                    'action'     => 'resume',
                ),
            ),
        ),
    ),
);

return $config;
