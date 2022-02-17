<?php

declare(strict_types=1);

return [
    'art' => [
        'error_notification' => [
            'sender' => $_SERVER['CONTACT_MESSAGE_SENDER_ADDRESS'] ?? '',
            'recipient' => $_SERVER['ERROR_NOTIFICATION_ADDRESS'] ?? '',
        ],
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
