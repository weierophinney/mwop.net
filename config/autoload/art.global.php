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
            'folder'    => $getSetting('ART_SPACES_FOLDER'),
        ],
    ],
];
