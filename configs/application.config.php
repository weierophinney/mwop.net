<?php
return new Zend\Config\Config(array(
    'module_paths' => array(
        realpath(__DIR__ . '/../modules'),
    ),
    'modules' => array(
        'Zf2Module', // paradox?!
        'Zf2Mvc',
        'Application',
        'Comic',
        'CommonResource',
        'Blog',
    ),
));
