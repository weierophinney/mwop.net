<?php
$config = array();
$config['disqus'] = array(
    'key'         => 'DISQUS KEY GOES HERE',
    'development' => 0,
);

$config['di'] = array(
'instance' => array(
    'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
        'map' => array(
            'blog/assets'       => __DIR__ . '/../view/blog/assets.phtml',
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
                'may_terminate' => false,
                'child_routes'  => array(
                    'index' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '.html',
                        ),
                    ),
                    'feed-atom' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '-atom.xml',
                        ),
                    ),
                    'feed-rss' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '-rss.xml',
                        ),
                    ),
                    'entry' => array(
                        'type'    => 'Regex',
                        'options' => array(
                            'regex' => '/(?<id>[^/]+)\.html',
                            'spec' => '/%id%.html',
                        ),
                    ),
                    'tag' => array(
                        'type'    => 'Regex',
                        'options' => array(
                            'regex' => '/tag/(?<tag>[^/.-]+)',
                            'defaults' => array(
                                'action'     => 'tag',
                            ),
                            'spec' => '/tag/%tag%',
                        ),
                        'may_terminate' => false,
                        'child_routes' => array(
                            'page' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '.html',
                                ),
                            ),
                            'feed-atom' => array(
                                'type'    => 'Literal',
                                'options' => array(
                                    'route' => '-atom.xml',
                                ),
                            ),
                            'feed-ress' => array(
                                'type'    => 'Literal',
                                'options' => array(
                                    'route' => '-ress.xml',
                                ),
                            ),
                        ),
                    ),
                    'year' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '/year/:year.html',
                            'constraints' => array(
                                'year' => '\d{4}',
                            ),
                            'defaults' => array(
                                'action'     => 'year',
                            ),
                        ),
                    ),
                    'month' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '/month/:year/:month.html',
                            'constraints' => array(
                                'year'  => '\d{4}',
                                'month' => '\d{2}',
                            ),
                            'defaults' => array(
                                'action'     => 'month',
                            ),
                        ),
                    ),
                    'day' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route' => '/day/:year/:month/:day.html',
                            'constraints' => array(
                                'year'  => '\d{4}',
                                'month' => '\d{2}',
                                'day'   => '\d{2}',
                            ),
                            'defaults' => array(
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
