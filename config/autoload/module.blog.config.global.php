<?php
$config = array();
$config['di'] = array(
'definition' => array('class' => array(
    'Mongo' => array(
        '__construct' => array(
            'server'  => array(
                'required' => false, 
                'type'     => false,
            ),
            'options' => array('required' => false),
        ),
    ),
    'MongoDB' => array(
        '__construct' => array(
            'conn' => array(
                'required' => true,
                'type'     => 'Mongo',
            ),
            'name' => array('required' => true),
        ),
    ),
    'MongoCollection' => array(
        '__construct' => array(
            'db' => array(
                'required' => true,
                'type'     => 'MongoDB',
            ),
            'name' => array('required' => true),
        ),
    ),
)),
'instance' => array(
    'Mongo' => array('parameters' => array(
        'server'  => 'SERVER DSN HERE',
    )),

    'MongoDB' => array( 'parameters' => array(
        'conn' => 'Mongo',
        'name' => 'DB NAME HERE',
    )),

    'MongoCollection' => array('parameters' => array(
        'db'   => 'MongoDB',
        'name' => 'COLLECTION NAME HERE',
    )),

    'Blog\EntryResource' => array('parameters' => array(
        'dataSource' => 'CommonResource\DataSource\Mongo',
        'class'      => 'CommonResource\Resource\MongoCollection',
    )), 

    'Blog\Controller\EntryController' => array('parameters' => array(
        'renderer' => 'Zend\View\Renderer\PhpRenderer',
        'resource' => 'Blog\EntryResource',
        'key'      => 'PATH TO API KEY GOES HERE',
    )),

    'CommonResource\DataSource\Mongo' => array('parameters' => array(
        'connection' => 'MongoCollection',
    )),

));

return $config;
