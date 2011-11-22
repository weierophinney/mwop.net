<?php
return new Zend\Config\Config(array(
    'modulePaths' => array(
        realpath(__DIR__ . '/../modules'),
        realpath(__DIR__ . '/../library/ZendFramework2/modules'),
    ),
    'modules' => array(
        'Zf2Module', // paradox?!
        'Zf2Mvc',
        'Application',
        'Sample',
    ),
));
