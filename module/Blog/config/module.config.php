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
    'blog' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/blog',
            'defaults' => array(
                'controller' => 'blog-entry',
            ),
        ),
        'may_terminate' => true,
        'child_routes'  => array(
            'feed' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '.xml',
                    'defaults' => array(
                        'controller' => 'blog-entry',
                        'format'     => 'xml',
                    ),
                ),
            ),
            'entry' => array(
                'type'    => 'Regex',
                'options' => array(
                    'regex' => '/(?<id>[^/]+)',
                    'defaults' => array(
                        'controller' => 'blog-entry',
                    ),
                    'spec' => '/%id%',
                ),
            ),
            'tag' => array(
                'type'    => 'Regex',
                'options' => array(
                    'regex' => '/tag/(?<tag>[^/.]+)',
                    'defaults' => array(
                        'controller' => 'blog-entry',
                        'action'     => 'tag',
                    ),
                    'spec' => '/tag/%tag%',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'feed' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            'route' => '.xml',
                            'defaults' => array(
                                'controller' => 'blog-entry',
                                'action'     => 'tag',
                                'format'     => 'xml',
                            ),
                        ),
                    ),
                ),
            ),
            'year' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/year/:year',
                    'constraints' => array(
                        'year' => '\d{4}',
                    ),
                    'defaults' => array(
                        'controller' => 'blog-entry',
                        'action'     => 'year',
                    ),
                ),
            ),
            'month' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/month/:year/:month',
                    'constraints' => array(
                        'year'  => '\d{4}',
                        'month' => '\d{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'blog-entry',
                        'action'     => 'month',
                    ),
                ),
            ),
            'day' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/day/:year/:month/:day',
                    'constraints' => array(
                        'year'  => '\d{4}',
                        'month' => '\d{2}',
                        'day'   => '\d{2}',
                    ),
                    'defaults' => array(
                        'controller' => 'blog-entry',
                        'action'     => 'day',
                    ),
                ),
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
        'key'      => 'data/api-key.txt',
    )),

    'view' => array( 'parameters' => array(
        'resolver' => 'view-resolver',
    )),

    'view-resolver' => array('parameters' => array(
        'paths' => array(
            'blog' => __DIR__ . '/../view',
        ),
    )),
));

return $config;
