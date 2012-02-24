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
    'Blog\EntryResource' => array('parameters' => array(
        'dataSource' => 'CommonResource\DataSource\Mock',
        'class'      => 'CommonResource\Resource\Collection',
    )), 

    'Blog\Controller\EntryController' => array('parameters' => array(
        'renderer' => 'Zend\View\Renderer\PhpRenderer',
        'resource' => 'Blog\EntryResource',
        'key'      => 'data/api-key.txt',
    )),

    'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
        'map' => array(
            'blog/blogroll'     => __DIR__ . '/../view/blog/blogroll.phtml',
            'blog/entry-short'  => __DIR__ . '/../view/blog/entry-short.phtml',
            'blog/entry'        => __DIR__ . '/../view/blog/entry.phtml',
            'blog/form'         => __DIR__ . '/../view/blog/form.phtml',
            'blog/list'         => __DIR__ . '/../view/blog/list.phtml',
            'blog/paginator'    => __DIR__ . '/../view/blog/paginator.phtml',
            'blog/social-media' => __DIR__ . '/../view/blog/social-media.phtml',
            'blog/tags'         => __DIR__ . '/../view/blog/tags.phtml',
        ),
    )),

    'Zend\View\Resolver\TemplatePathStack' => array('parameters' => array(
        'paths' => array(
            'blog' => __DIR__ . '/../view',
        ),
    )),
    
    'Zend\Mvc\Router\RouteStack' => array('parameters' => array(
        'routes' => array(
            'blog' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/blog',
                    'defaults' => array(
                        'controller' => 'Blog\Controller\EntryController',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'feed' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '.xml',
                            'defaults' => array(
                                'controller' => 'Blog\Controller\EntryController',
                                'format'     => 'xml',
                            ),
                        ),
                    ),
                    'entry' => array(
                        'type'    => 'Regex',
                        'options' => array(
                            'regex' => '/(?<id>[^/]+)',
                            'defaults' => array(
                                'controller' => 'Blog\Controller\EntryController',
                            ),
                            'spec' => '/%id%',
                        ),
                    ),
                    'tag' => array(
                        'type'    => 'Regex',
                        'options' => array(
                            'regex' => '/tag/(?<tag>[^/.]+)',
                            'defaults' => array(
                                'controller' => 'Blog\Controller\EntryController',
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
                                        'controller' => 'Blog\Controller\EntryController',
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
                                'controller' => 'Blog\Controller\EntryController',
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
                                'controller' => 'Blog\Controller\EntryController',
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
                                'controller' => 'Blog\Controller\EntryController',
                                'action'     => 'day',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    )),
));

return $config;
