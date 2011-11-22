<?php
return new Zend\Config\Config(array(
    'module_paths' => array(
        realpath(__DIR__ . '/../module'),
    ),
    'modules' => array(
        'Application',
        'Authentication',
        'Comic',
        'CommonResource',
        'Blog',
        'Contact',
        'Local',
    ),
));
