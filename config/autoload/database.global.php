<?php
return array(
    'db' => array(
        'driver' => 'Pdo_Sqlite',
        'database' => getcwd() . '/data/users.db',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
