<?php

declare(strict_types=1);

namespace Mwop;

use JsonException;

use function json_decode;

class JsonValidate
{
    public function __invoke(string $json): bool
    {
        try {
            json_decode($json);
            return true;
        } catch (JsonException) {
            return false;
        }
    }
}
