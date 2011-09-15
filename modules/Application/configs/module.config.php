<?php
$config = array();
$config['production'] = array(
    'routes' => array(
        'home' => array(
            'type'    => 'Zf2Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/',
                'defaults' => array(
                    'controller' => 'Application\Controller\PageController',
                    'page'       => 'home',
                ),
            ),
        ),
        'comics' => array(
            'type'    => 'Zf2Mvc\Router\Http\Literal',
            'options' => array(
                'route' => '/comics',
                'defaults' => array(
                    'controller' => 'Application\Controller\PageController',
                    'page'       => 'comics',
                ),
            ),
        ),
        'resume' => array(
            'type'    => 'Zf2Mvc\Router\Http\Literal',
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
return $config;
