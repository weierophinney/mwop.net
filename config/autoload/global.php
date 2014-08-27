<?php
return [
    'services' => [
        'invokables' => [
            'not-allowed' => 'Mwop\NotAllowed',
            'query-params' => 'Mwop\QueryParams',
        ],
        'factories' => [
            'page.home' => 'Mwop\Factory\HomePage',
            'page.resume' => 'Mwop\Factory\ResumePage',
            'renderer'  => 'Mwop\Factory\Renderer',
        ],
    ],
];
