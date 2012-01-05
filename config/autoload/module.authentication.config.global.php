<?php
$config = array(
    'di' => array('instance' => array(
        'Authentication\AuthenticationService' => array('parameters' => array(
            'filename' => 'PATH TO DIGEST',
            'realm'    => 'REALM',
        )),
    )),
);

return $config;
