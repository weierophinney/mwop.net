<?php
$config = array();

$config['di'] = array(
    'definition' => array('class' => array(
        'Cache\Listener' => array(
            '__construct' => array(
                'cache' => array(
                    'required' => true,
                    'type'     => 'Zend\Cache\Frontend',
                ),
            ),
        ),
    )),
);

$config['cache'] = array(
    'frontend' => 'Core',
    'backend'  => 'BlackHole',
    'frontend_options' => array(
        'caching'                 => false,
        'cache_id_prefix'         => 'cache_listener',
        'lifetime'                => 60 * 60 * 24 * 7, // 1 week
        'automatic_serialization' => true,
    ),
    'backend_options' => array(
    ),
);

$config = array(
    'production'  => $config,
    'staging'     => $config,
    'testing'     => $config,
    'development' => $config,
);

return $config;
