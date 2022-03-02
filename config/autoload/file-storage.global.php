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
    'file-storage' => [
        'endpoint' => $getSetting('SPACES_ENDPOINT'),
        'region'   => $getSetting('SPACES_REGION'),
        'key'      => $getSetting('SPACES_KEY'),
        'secret'   => $getSetting('SPACES_SECRET'),
        'bucket'   => $getSetting('SPACES_BUCKET'),
    ],
];
