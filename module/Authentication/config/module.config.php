<?php
$config = array(
    'di' => array('instance' => array(
        'Authentication\AuthenticationController' => array('parameters' => array(
            'auth' => 'Authentication\AuthenticationService',
        )),

        'Authentication\AuthenticationListener' => array('parameters' => array(
            'auth' => 'Authentication\AuthenticationService',
            'view' => 'Zend\View\View',
        )),

        'Authentication\AuthenticationService' => array('parameters' => array(
            'filename' => 'PATH TO DIGEST',
            'realm'    => 'REALM',
        )),

        'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
            'map' => array(
                'authentication/401'    => __DIR__ . '/../view/authentication/401.phtml',
                'authentication/login'  => __DIR__ . '/../view/authentication/login.phtml',
                'authentication/logout' => __DIR__ . '/../view/authentication/logout.phtml',
            ),
        )),

        'Zend\View\Resolver\TemplatePathStack' => array('parameters' => array(
            'paths' => array(
                'authentication' => __DIR__ . '/../view',
            ),
        )),

        'Zend\Mvc\Router\RouteStack' => array('parameters' => array(                                          
            'routes' => array(
                'authentication-login' => array(
                    'type'    => 'Literal',
                    'options' => array(
                        'route' => '/login',
                        'defaults' => array(
                            'controller' => 'Authentication\AuthenticationController',
                            'action'     => 'login',
                        ),
                    ),
                ),
                'authentication-logout' => array(
                    'type'    => 'Literal',
                    'options' => array(
                        'route' => '/logout',
                        'defaults' => array(
                            'controller' => 'Authentication\AuthenticationController',
                            'action'     => 'logout',
                        ),
                    ),
                ),
            ),
        )),
    )),
);

return $config;
