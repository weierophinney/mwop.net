<?php
return array(
    'db_params' => array(
        'dsn'      => 'sqlite:' . getcwd() . '/data/users.db',
        'database' => '',
        'username' => '',
        'password' => '',
        'hostname' => '',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) {
                $config = $sm->get('config');
                $config = $config['db_params'];
                return new Zend\Db\Adapter\Adapter(array(
                    'driver'   => 'pdo',
                    'dsn'      => $config['dsn'],
                    'database' => $config['database'],
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'hostname' => $config['hostname'],
                ));
            },
        ),
    ),
);
