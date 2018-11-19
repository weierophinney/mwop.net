<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

return [
    'blog' => [
        'db'     => 'sqlite:' . realpath(getcwd()) . '/data/posts.db',
        'cache'  => [
            'enabled' => true,
        ],
        'disqus' => [
            'developer' => 0,
            'key'       => null,
        ],
    ],
    'contact' => [
        'recaptcha_pub_key'  => null,
        'recaptcha_priv_key' => null,
        'message' => [
            'to'   => null,
            'from' => null,
            'sender' => [
                'address' => null,
                'name'    => null,
            ],
        ],
    ],
    'debug' => false,
    'github' => [
        'user'  => 'weierophinney',
        'limit' => 10,
    ],
    'homepage' => [
        'feed-count' => 10,
        'feeds' => [
            [
                'url' => realpath(getcwd()) . '/data/feeds/rss.xml',
                'sitename' => 'mwop.net',
                'siteurl' => 'https://mwop.net/blog',
            ],
            [
                'url' => 'https://blog.zend.com/author/matthew-wop/feed/',
                'favicon' => 'https://pbs.twimg.com/profile_images/603690040602927104/0bp-4InR_bigger.jpg',
                'sitename' => 'Zend Blog',
                'siteurl' => 'https://blog.zend.com/author/matthew-wop/',
            ],
            [
                'url' => 'https://devzone.zend.com/author/mwop/feed/',
                'favicon' => 'https://pbs.twimg.com/profile_images/603690040602927104/0bp-4InR_bigger.jpg',
                'sitename' => 'Zend Developer Zone',
                'siteurl' => 'https://devzone.zend.com/author/mwop/',
            ],
            [
                'url' => 'https://framework.zend.com/blog/feed-rss.xml',
                'favicon' => 'https://framework.zend.com/ico/favicon.ico',
                'sitename' => 'Zend Framework Blog',
                'siteurl' => 'https://framework.zend.com/blog/',
                'filters' => [
                    function ($entry) {
                        return (false !== strpos($entry->getAuthor()['name'], 'Phinney'));
                    },
                ],
            ],
        ],
    ],
    'mail' => [
        'transport' => [
            'class' => 'Zend\Mail\Transport\Smtp',
            'options' => [
                'host' => null,
            ],
        ],
    ],
    'oauth2' => [
        'github' => [
            'clientId'     => null,
            'clientSecret' => null,
            'redirectUri'  => null,
        ],
        'google' => [
            'clientId'     => null,
            'clientSecret' => null,
            'redirectUri'  => null,
            // Enable this to restrict authentication to users at the listed domain:
            // 'hostedDomain' => 'https://mwop.net',
        ],
    ],
];
