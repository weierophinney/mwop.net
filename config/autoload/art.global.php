<?php

declare(strict_types=1);

$getSetting = function (string $key): string {
    $value = getenv($key);
    if (false !== $value) {
        return $value;
    }

    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    return $_SERVER[$key] ?? '';
};

return [
    'art' => [
        'error_notification' => [
            'sender'    => $getSetting('CONTACT_MESSAGE_SENDER_ADDRESS'),
            'recipient' => $getSetting('ERROR_NOTIFICATION_ADDRESS'),
        ],
        'storage'            => [
            'endpoint' => $getSetting('ART_SPACES_ENDPOINT'),
            'region'   => $getSetting('ART_SPACES_REGION'),
            'bucket'   => $getSetting('ART_SPACES_BUCKET'),
            'folder'   => $getSetting('ART_SPACES_FOLDER'),
            'key'      => $getSetting('ART_SPACES_KEY'),
            'secret'   => $getSetting('ART_SPACES_SECRET'),
        ],
    ],
];
