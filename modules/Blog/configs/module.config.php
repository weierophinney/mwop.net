<?php
$config = array();
$config['authentication'] = array(
    'Blog\Controller\EntryController' => array(
        'preview',
    ),
);

$config['disqus'] = array(
    'key'         => 'DISQUS KEY GOES HERE',
    'development' => 0,
);

$config['routes'] = array(
    'blog-create-form' => array(
        'type'    => 'Literal',
        'options' => array(
            'route' => '/blog/admin/create',
            'defaults' => array(
                'controller' => 'blog-entry',
                'action'     => 'create',
            ),
        ),
    ),
    'blog-tag' => array(
        'type'    => 'Regex',
        'options' => array(
            'regex' => '/blog/tag/(?<tag>[^/]+)',
            'defaults' => array(
                'controller' => 'blog-entry',
                'action'     => 'tag',
            ),
            'spec' => '/blog/tag/%tag%',
        ),
    ),
    'blog-tag-feed' => array(
        'type'    => 'Regex',
        'options' => array(
            'regex' => '/blog/tag/(?<tag>[^/]+)\\.xml',
            'defaults' => array(
                'controller' => 'blog-entry',
                'action'     => 'tag',
                'format'     => 'xml',
            ),
            'spec' => '/blog/tag/%tag%.xml',
        ),
    ),
    'blog-year' => array(
        'type'    => 'Regex',
        'options' => array(
            'regex' => '/blog/year/(?<year>\d{4})',
            'defaults' => array(
                'controller' => 'blog-entry',
                'action'     => 'year',
            ),
            'spec' => '/blog/year/%year%',
        ),
    ),
    'blog-month' => array(
        'type'    => 'Regex',
        'options' => array(
            'regex' => '/blog/month/(?<year>\d{4})/(?<month>\d{1,2})',
            'defaults' => array(
                'controller' => 'blog-entry',
                'action'     => 'month',
            ),
            'spec' => '/blog/month/%year%/%month%',
        ),
    ),
    'blog-day' => array(
        'type'    => 'Regex',
        'options' => array(
            'regex' => '/blog/day/(?<year>\d{4})/(?<month>\d{1,2})/(?<day>\d{1,2})',
            'defaults' => array(
                'controller' => 'blog-entry',
                'action'     => 'day',
            ),
            'spec' => '/blog/day/%year%/%month%/%day%',
        ),
    ),
    'blog-entry' => array(
        'type'    => 'Regex',
        'options' => array(
            'regex' => '/blog/(?<id>[^/]+)',
            'defaults' => array(
                'controller' => 'blog-entry',
            ),
            'spec' => '/blog/%id%',
        ),
    ),
    'blog' => array(
        'type'    => 'Literal',
        'options' => array(
            'route' => '/blog',
            'defaults' => array(
                'controller' => 'blog-entry',
            ),
        ),
    ),
    'blog-feed' => array(
        'type'    => 'Literal',
        'options' => array(
            'route' => '/blog.xml',
            'defaults' => array(
                'controller' => 'blog-entry',
                'format'     => 'xml',
            ),
        ),
    ),
);

$config['di'] = array(
'definition' => array('class' => array(
    'Blog\EntryResource' => array(
        'setCollectionClass' => array(
            'class' => array(
                'required' => false,
                'type'     => false,
            ),
        ),
    ),
    'Blog\Controller\EntryController' => array(
        'setApiKeyLocation' => array(
            'key' => array(
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
        'blog-entry'      => 'Blog\Controller\EntryController',
    ),

    'Blog\EntryResource' => array('parameters' => array(
        'dataSource' => 'CommonResource\DataSource\Mock',
        'class'      => 'CommonResource\Resource\Collection',
    )), 

    'Blog\Controller\EntryController' => array('parameters' => array(
        'view'     => 'view',
        'resource' => 'Blog\EntryResource',
        'key'      => APPLICATION_PATH . '/data/api-key.txt',
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

return $config;
