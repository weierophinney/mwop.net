<?php
return array('di' => array(
    'definition' => array('class' => array(
        'GithubFeed\AtomReader' => array(
            '__construct' => array(
                'required' => true,
                'user' => array(
                    'required' => true,
                    'type'     => false,
                ),
                'token' => array(
                    'required' => true,
                    'type'     => false,
                ),
            ),
            'setLimit' => array(
                'required' => false,
                'limit' => array(
                    'required' => true,
                    'type'     => false,
                ),
            ),
        ),
    )),
    'instance' => array(
        'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
            'map' => array(
                'github-feed/links' => __DIR__ . '/../view/github-feed/links.phtml',
            ),
        )),
        'Zend\View\Resolver\TemplatePathStack' => array('parameters' => array(
            'paths' => array(
                'github-feed' => __DIR__ . '/../view',
            ),
        )),
    ),
));
