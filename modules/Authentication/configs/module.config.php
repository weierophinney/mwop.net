<?php
$config['production'] = array(
    'routes' => array(
        'authentication-login' => array(
            'type'    => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/login',
                'defaults' => array(
                    'controller' => 'Authentication\AuthenticationController',
                    'action'     => 'login',
                ),
            ),
        ),
        'authentication-logout' => array(
            'type'    => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/logout',
                'defaults' => array(
                    'controller' => 'Authentication\AuthenticationController',
                    'action'     => 'logout',
                ),
            ),
        ),
    ),

    'di' => array('instance' => array(
        'alias' => array(
            'view' => 'Zend\View\PhpRenderer',
            'view-resolver' => 'Zend\View\TemplatePathStack',
        ),

        'Authentication\AuthenticationController' => array('parameters' => array(
            'auth' => 'Authentication\AuthenticationService',
        )),

        'Authentication\AuthenticationListener' => array('parameters' => array(
            'auth'     => 'Authentication\AuthenticationService',
            'renderer' => 'view',
        )),

        'Authentication\AuthenticationService' => array('parameters' => array(
            'filename' => APPLICATION_PATH . '/data/htdigest',
            'realm'    => 'mwop',
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
$config['testing']['di']['instance']['Authentication\AuthenticationService']['parameters']['filename'] = __DIR__ . '/../tests/Authentication/_files/htdigest.txt';

$config['development'] = $config['production'];

return $config;
