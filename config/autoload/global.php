<?php
return [
    'contact' => [
        'recaptcha_pub_key'  => null,
        'recaptcha_priv_key' => null,
    ],
    'github' => [
        'user'  => 'weierophinney',
        'token' => null,
        'limit' => 4,
    ],
    'services' => [
        'invokables' => [
            'not-allowed'  => 'Mwop\NotAllowed',
            'query-params' => 'Mwop\QueryParams',
        ],
        'factories' => [
            'contact'                => 'Mwop\Contact\Factory\Contact',
            'contact.landing'        => 'Mwop\Contact\Factory\LandingPage',
            'contact.thankyou'       => 'Mwop\Contact\Factory\ThankYouPage',
            'http'                   => 'Mwop\Factory\HttpClient',
            'page.home'              => 'Mwop\Factory\HomePage',
            'page.resume'            => 'Mwop\Factory\ResumePage',
            'renderer'               => 'Mwop\Factory\Renderer',
            'session'                => 'Mwop\Factory\Session',
            'Mwop\Github\AtomReader' => 'Mwop\Factory\AtomReader',
            'Mwop\Github\Fetch'      => 'Mwop\Factory\GithubFetch',
        ],
    ],
];
