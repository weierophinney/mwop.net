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
        'Authentication\AuthenticationController' => array('parameters' => array(
            'auth' => 'Authentication\AuthenticationService',
        )),
        'Authentication\AuthenticationListener' => array('parameters' => array(
            'auth'     => 'Authentication\AuthenticationService',
            'renderer' => 'Zend\View\PhpRenderer',
        )),
        'Authentication\AuthenticationService' => array('parameters' => array(
            'filename' => APPLICATION_PATH . '/data/htdigest',
            'realm'    => 'mwop',
        )),
        'Zend\View\PhpRenderer' => array(
            'methods' => array(
                'setResolver' => array(
                    'resolver' => 'Zend\View\TemplatePathStack',
                    'options' => array(
                        'script_paths' => array(
                            'authentication' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    )),
);

$config['staging']     = $config['production'];

$config['testing']     = $config['production'];
$config['testing']['di']['instance']['Authentication\AuthenticationService']['parameters']['filename'] = __DIR__ . '/../tests/Authentication/_files/htdigest.txt';

$config['development'] = $config['production'];

return $config;
