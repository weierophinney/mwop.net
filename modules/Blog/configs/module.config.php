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
    'Blog\EntryResource' => array(
        'setCollectionClass' => array(
            'class' => array(
                'required' => false,
                'type'     => false,
            ),
        ),
    ),
)),
'instance' => array(
    'alias' => array(
        'view'            => 'Zend\View\PhpRenderer',
        'view-resolver'   => 'Zend\View\TemplatePathStack',
    ),

    'Mongo' => array('parameters' => array(
        'server'  => 'mongodb://localhost:27017',
    )),

    'MongoDB' => array( 'parameters' => array(
        'conn' => 'Mongo',
        'name' => 'wopnet',
    )),

    'MongoCollection' => array('parameters' => array(
        'db'   => 'MongoDB',
        'name' => 'entries',
    )),

    'Blog\EntryResource' => array('parameters' => array(
        'dataSource' => 'CommonResource\DataSource\Mongo',
        'class'      => 'CommonResource\Resource\MongoCollection',
    )), 

    'Blog\Controller\EntryController' => array('parameters' => array(
        'view'     => 'view',
        'resource' => 'Blog\EntryResource',
    ), 'methods' => array(
        'setApiKeyLocation' => array(
            'key' => APPLICATION_PATH . '/data/api-key.txt',
        ),
    )),

    'CommonResource\DataSource\Mongo' => array('parameters' => array(
        'connection' => 'MongoCollection',
    )),

    'view' => array( 'parameters' => array(
        'resolver' => 'view-resolver',
    )),

    'view-resolver' => array('parameters' => array(
        'paths' => array(
            'blog' => __DIR__ . '/../views',
        ),
    )),
));

$config = array(
    'production'  => $config,
    'staging'     => $config,
    'testing'     => $config,
    'development' => $config,
);

$config['testing']['di']['instance']['MongoDB']['parameters']['name'] = 'importtest';
$config['development']['di']['instance']['MongoDB']['parameters']['name'] = 'mwoptest';

return $config;
