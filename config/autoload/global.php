<?php
return [
    'blog' => [
        'db'            => 'sqlite:' . realpath(getcwd()) . '/data/posts.db',
        'cache_path'    => 'data/cache/posts',
        'cache_enabled' => true,
        'disqus'        => [
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
        'limit' => 4,
    ],
    'homepage' => [
        'feeds' => [
            realpath(getcwd()) . '/data/feeds/rss.xml',
            'http://blog.zend.com/author/matthew-wop/feed/',
            'https://devzone.zend.com/author/mwop/feed/',
            [
                'url' => 'https://framework.zend.com/blog/feed-rss.xml',
                'each' => function ($entry) {
                    $link = $entry->getLink();
                    $r = new ReflectionProperty($entry, 'data');
                    $r->setAccessible(true);
                    $data = $r->getValue($entry);
                    $data['links'][0] = 'https://framework.zend.com' . $link;
                    $r->setValue($entry, $data);
                },
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
    'opauth' => [
        'path'               => '/auth/',
        'callback_url'       => '/auth/callback',
        'callback_transport' => 'session',
        'debug'              => false,
        'security_salt'      => 'PROVIDE A PROPER SALT',
        'Strategy'           => [
            'GitHub' => [
                'client_id'     => null,
                'client_secret' => null,
            ],
            'Google' => [
                'client_id'     => null,
                'client_secret' => null,
                'scope'         => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
            ],
            'Twitter' => [
                'key'           => null,
                'secret'        => null,
            ],
        ],
    ],

    // Trick zf-deploy into thinking this is a ZF2 app so it can build a package.
    'modules' => [],
];
