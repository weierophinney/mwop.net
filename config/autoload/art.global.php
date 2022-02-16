<?php

declare(strict_types=1);

return [
    'art' => [
        'storage' => [
            'endpoint' => $_SERVER['ART_SPACES_ENDPOINT'] ?? '',
            'region'   => $_SERVER['ART_SPACES_REGION'] ?? '',
            'bucket'   => $_SERVER['ART_SPACES_BUCKET'] ?? '',
            'folder'   => $_SERVER['ART_SPACES_FOLDER'] ?? '',
            'key'      => $_SERVER['ART_SPACES_KEY'] ?? '',
            'secret'   => $_SERVER['ART_SPACES_SECRET'] ?? '',
        ],
    ],
];
