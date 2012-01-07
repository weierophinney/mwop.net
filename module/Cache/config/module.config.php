<?php
$config = array();

$config['di'] = array(
    'definition' => array('class' => array(
        'Cache\Listener' => array(
            '__construct' => array(
                'cache' => array(
                    'required' => true,
                    'type'     => 'Zend\Cache\Storage\Adapter',
                ),
            ),
        ),
    )),

    'preferences' => array(
        'Zend\Cache\Storage\Adapter' => 'Zend\Cache\Storage\Adapter\Memcached',
    ),

    'instance' => array(
        'Zend\Cache\Storage\Adapter\Memcached' => array('parameters' => array(
            'options' => 'Zend\Cache\Storage\Adapter\MemcachedOptions',
        )),

        'Zend\Cache\Storage\Adapter\MemcachedOptions' => array('parameters' => array(
            'options' => array(
                // 'caching'                 => true,
                'namespace' => 'cache_listener',
                'ttl' => 60 * 60 * 24 * 7, // 1 week
                'server' => '127.0.0.1',
                'port' => 11211,
                'compression' => true,
                'binary_protocol' => true,
                'no_block' => true,
                'connect_timeout' => 100,
                'serializer' => 3, // JSON
            ),
        )),
    ),
);

return $config;
