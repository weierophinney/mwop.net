<?php
$config['production'] = array(
    'di' => array('instance' => array(
        'Authentication\AuthenticationService' => array('parameters' => array(
            'filename' => 'PATH TO DIGEST',
            'realm'    => 'REALM',
        )),
    )),
);

$config['staging']     = $config['production'];
$config['testing']     = $config['production'];
$config['development'] = $config['production'];

return $config;
