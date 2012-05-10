<?php
$config = array();
$config = array(
    'authentication' => array(
        'Application\Controller\PageController' => array(
            'comics',
        ),
    ),

    'view' => array(
        'dojo-config' => 'dojo-config.phtml',
        'layout'      => 'layout.phtml',
        'search'      => array(
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
        'Zend\Mvc\Router\RouteStackInterface' => array(
            'instantiator' => array(
                'Zend\Mvc\Router\Http\TreeRouteStack',
                'factory'
            ),
        ),
    )),
    'instance' => array(
        'alias' => array(
            'application-page' => 'Application\Controller\PageController',
        ),

        'Zend\View\HelperLoader' => array('parameters' => array(
            'map' => array(
                'disqus' => 'Application\View\Helper\Disqus',
            ),
        )),

        'Zend\View\HelperBroker' => array('parameters' => array(
            'loader' => 'Zend\View\HelperLoader',
        )),

        'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
            'map' => array(
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
        )),

        'Zend\View\Resolver\TemplatePathStack' => array('parameters' => array(
            'paths' => array(
                'application' => __DIR__ . '/../view',
            ),
        )),

        'Zend\View\Resolver\AggregateResolver' => array('injections' => array(
            'Zend\View\Resolver\TemplateMapResolver',
            'Zend\View\Resolver\TemplatePathStack',
        )),

        'Zend\View\Renderer\PhpRenderer' => array('parameters' => array(
            'resolver' => 'Zend\View\Resolver\AggregateResolver',
            'broker'   => 'Zend\View\HelperBroker',
        )),

        'Zend\Mvc\View\DefaultRenderingStrategy' => array('parameters' => array(
                'baseTemplate' => 'layout',
            ),
        ),

        'Zend\Mvc\View\ExceptionStrategy' => array('parameters' => array(
                'displayExceptions' => true,
                'template'          => 'error',
            ),
        ),

        'Zend\Mvc\View\RouteNotFoundStrategy' => array('parameters' => array(
                'notFoundTemplate' => 'pages/404',
            ),
        ),

        'Application\View\Helper\Disqus' => array('parameters' => array(
            'options' => array(),
        )),

        'Zend\Mvc\Router\RouteStackInterface' => array('parameters' => array(                                          
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
        )),
    )),
);

return $config;
