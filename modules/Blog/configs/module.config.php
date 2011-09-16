<?php
$config = array();
$config['routes'] = array(
    'blog-create-form' => array(
        'type'    => 'Zf2Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/blog/admin/create',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'create',
            ),
        ),
    ),
    'blog-tag-feed' => array(
        'type'    => 'Zf2Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/tag/(?P<tag>[^/]+)\\.xml',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'tag',
                'format'     => 'xml',
            ),
            'spec' => '/blog/tag/%s.xml',
        ),
    ),
    'blog-tag' => array(
        'type'    => 'Zf2Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/tag/(?P<tag>[^/]+)',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'tag',
            ),
            'spec' => '/blog/tag/%s',
        ),
    ),
    'blog-year' => array(
        'type'    => 'Zf2Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/year/(?P<year>\d{4})',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'year',
            ),
            'spec' => '/blog/year/%s',
        ),
    ),
    'blog-month' => array(
        'type'    => 'Zf2Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/month/(?P<year>\d{4})/(?P<month>\d{2})',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'month',
            ),
            'spec' => '/blog/month/%s/%s',
        ),
    ),
    'blog-day' => array(
        'type'    => 'Zf2Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/day/(?P<year>\d{4})/(?P<month>\d{2})/(?P<day>\d{2})',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'action'     => 'day',
            ),
            'spec' => '/blog/day/%s/%s/%s',
        ),
    ),
    'blog-entry' => array(
        'type'    => 'Zf2Mvc\Router\Http\Regex',
        'options' => array(
            'regex' => '/blog/(?P<id>[^/]+)',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
            ),
            'spec' => '/blog/%s',
        ),
    ),
    'blog-feed' => array(
        'type'    => 'Zf2Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/blog.xml',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
                'format'     => 'xml',
            ),
        ),
    ),
    'blog' => array(
        'type'    => 'Zf2Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/blog',
            'defaults' => array(
                'controller' => 'Blog\Controller\EntryController',
            ),
        ),
    ),
);

$config['di'] = array('instance' => array(
    'CommonResource\Mongo' => array( 'parameters' => array(
        'server'  => 'mongodb://localhost:27017',
        'options' => array('connect' => true),
    )),
    'CommonResource\MongoDB' => array( 'parameters' => array(
        'conn' => 'CommonResource\Mongo',
        'name' => 'mwoptest',
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

return array(
    'production'  => $config,
    'staging'     => $config,
    'testing'     => $config,
    'development' => $config,
);
