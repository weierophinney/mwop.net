<?php
return [
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
            'http'                   => 'Mwop\Factory\HttpClient',
            'page.home'              => 'Mwop\Factory\HomePage',
            'page.resume'            => 'Mwop\Factory\ResumePage',
            'renderer'               => 'Mwop\Factory\Renderer',
            'Mwop\Github\AtomReader' => 'Mwop\Factory\AtomReader',
            'Mwop\Github\Fetch'      => 'Mwop\Factory\GithubFetch',
        ],
    ],
];
