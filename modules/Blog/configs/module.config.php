<?php
$config = array();
$config['authentication'] = array(
    'Blog\Controller\EntryController' => array(
        'preview',
    ),
);

$config['routes'] = array(
    'blog-create-form' => array(
        'type'    => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/blog/admin/create',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'create',
            ),
        ),
    ),
    'blog-tag' => array(
        'type'    => 'Zend\Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/tag/(?P<tag>[^/]+)',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'tag',
            ),
            'spec' => '/blog/tag/%tag%',
        ),
    ),
    'blog-tag-feed' => array(
        'type'    => 'Zend\Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/tag/(?P<tag>[^/]+)\\.xml',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'tag',
                'format'     => 'xml',
            ),
            'spec' => '/blog/tag/%tag%.xml',
        ),
    ),
    'blog-year' => array(
        'type'    => 'Zend\Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/year/(?P<year>\d{4})',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'year',
            ),
            'spec' => '/blog/year/%year%',
        ),
    ),
    'blog-month' => array(
        'type'    => 'Zend\Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/month/(?P<year>\d{4})/(?P<month>\d{1,2})',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'month',
            ),
            'spec' => '/blog/month/%year%/%month%',
        ),
    ),
    'blog-day' => array(
        'type'    => 'Zend\Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/day/(?P<year>\d{4})/(?P<month>\d{1,2})/(?P<day>\d{1,2})',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'day',
            ),
            'spec' => '/blog/day/%year%/%month%/%day%',
        ),
    ),
    'blog-entry' => array(
        'type'    => 'Zend\Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/(?P<id>[^/]+)',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
            ),
            'spec' => '/blog/%id%',
        ),
    ),
    'blog' => array(
        'type'    => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/blog',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
            ),
        ),
    ),
    'blog-feed' => array(
        'type'    => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/blog.xml',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'format'     => 'xml',
            ),
        ),
    ),
);

$config['di'] = array('instance' => array(
    'alias' => array(
        'Mongo'           => 'CommonResource\Mongo',
        'MongoDB'         => 'CommonResource\MongoDB',
        'MongoCollection' => 'CommonResource\MongoCollection',
    ),

    'CommonResource\Mongo' => array( 'methods' => array(
        '__construct' => array(
            'server'  => 'mongodb://localhost:27017',
            'options' => array('connect' => true),
        )
    )),
    'CommonResource\MongoDB' => array( 'methods' => array(
        'conn' => 'CommonResource\Mongo',
        'name' => 'wopnet',
    )),
    'CommonResource\MongoCollection' => array('parameters' => array(
        'db'   => 'CommonResource\MongoDB',
        'name' => 'entries',
    )),
    'Blog\EntryResource' => array('parameters' => array(
        'dataSource'      => 'CommonResource\DataSource\Mongo',
        'collectionClass' => 'CommonResource\Resource\MongoCollection',
    ), 'methods' => array(
        'setCollectionClass' => array(
            'class' => 'CommonResource\Resource\MongoCollection',
        ),
    )), 
    'Blog\Controller\EntryController' => array('parameters' => array(
        'view'     => 'Zend\View\PhpRenderer',
        'resource' => 'Blog\EntryResource',
    ), 'methods' => array(
        'setApiKeyLocation' => array(
            'key' => APPLICATION_PATH . '/data/api-key.txt',
        ),
    )),
    'CommonResource\DataSource\Mongo' => array('parameters' => array(
        'connection' => 'CommonResource\MongoCollection',
    )),
    'Zend\View\PhpRenderer' => array(
        'methods' => array(
            'setResolver' => array(
                'resolver' => 'Zend\View\TemplatePathStack',
                'options' => array(
                    'script_paths' => array(
                        'blog' => __DIR__ . '/../views',
                    ),
                ),
            ),
        ),
    ),
));

$config = array(
    'production'  => $config,
    'staging'     => $config,
    'testing'     => $config,
    'development' => $config,
);

$config['testing']['di']['instance']['CommonResource\MongoDB']['parameters']['name'] = 'importtest';
$config['development']['di']['instance']['CommonResource\MongoDB']['parameters']['name'] = 'mwoptest';

return $config;
