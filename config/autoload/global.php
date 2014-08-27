<?php
return [
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
    'github' => [
        'user'  => 'weierophinney',
        'token' => null,
        'limit' => 4,
    ],
    'mail' => [
        'transport' => [
            'class' => 'Zend\Mail\Transport\Smtp',
            'options' => [
                'host' => null,
            ],
        ],
    ],
    'services' => [
        'invokables' => [
            'body-params'  => 'Mwop\BodyParams',
            'not-allowed'  => 'Mwop\NotAllowed',
            'query-params' => 'Mwop\QueryParams',
        ],
        'factories' => [
            'contact'                => 'Mwop\Contact\Factory\Contact',
            'contact.landing'        => 'Mwop\Contact\Factory\LandingPage',
            'contact.process'        => 'Mwop\Contact\Factory\Process',
            'contact.thankyou'       => 'Mwop\Contact\Factory\ThankYouPage',
            'http'                   => 'Mwop\Factory\HttpClient',
            'mail.transport'         => 'Mwop\Factory\MailTransport',
            'page.home'              => 'Mwop\Factory\HomePage',
            'page.resume'            => 'Mwop\Factory\ResumePage',
            'renderer'               => 'Mwop\Factory\Renderer',
            'session'                => 'Mwop\Factory\Session',
            'Mwop\Github\AtomReader' => 'Mwop\Factory\AtomReader',
            'Mwop\Github\Fetch'      => 'Mwop\Factory\GithubFetch',
        ],
    ],
];
