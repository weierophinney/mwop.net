<?php
$dbParams = array(
    'dsn'      => 'sqlite:' . getcwd() . '/data/users.db',
    'database' => '',
    'username' => '',
    'password' => '',
    'hostname' => '',
);
return array(
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($sm) use ($dbParams) {
                return new Zend\Db\Adapter\Adapter(array(
                    'driver'   => 'pdo',
                    'dsn'      => $dbParams['dsn'],
                    'database' => $dbParams['database'],
                    'username' => $dbParams['username'],
                    'password' => $dbParams['password'],
                    'hostname' => $dbParams['hostname'],
                ));
            },
        ),
    ),
);
