<?php
$config['production'] = array(
    'routes' => array(
        'authentication-login' => array(
            'type'    => 'Literal',
            'options' => array(
                'route' => '/login',
                'defaults' => array(
                    'controller' => 'authentication-authentication',
                    'action'     => 'login',
                ),
            ),
        ),
        'authentication-logout' => array(
            'type'    => 'Literal',
            'options' => array(
                'route' => '/logout',
                'defaults' => array(
                    'controller' => 'authentication-authentication',
                    'action'     => 'logout',
                ),
            ),
        ),
    ),

    'di' => array('instance' => array(
        'alias' => array(
            'view'                          => 'Zend\View\PhpRenderer',
            'view-resolver'                 => 'Zend\View\TemplatePathStack',
            'authentication-authentication' => 'Authentication\AuthenticationController',
        ),

        'Authentication\AuthenticationController' => array('parameters' => array(
            'auth' => 'Authentication\AuthenticationService',
        )),

        'Authentication\AuthenticationListener' => array('parameters' => array(
            'auth'     => 'Authentication\AuthenticationService',
            'renderer' => 'view',
        )),

        'Authentication\AuthenticationService' => array('parameters' => array(
            'filename' => 'PATH TO DIGEST',
            'realm'    => 'REALM',
        )),

        'view' => array('parameters' => array(
            'resolver' => 'view-resolver',
        )),

        'view-resolver' => array('parameters' => array(
            'paths' => array(
                'authentication' => __DIR__ . '/../views',
            ),
        )),
    )),
);

$config['staging']     = $config['production'];
$config['testing']     = $config['production'];
$config['development'] = $config['production'];

return $config;
