<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Contact\Validation;

use Laminas\Validator\ValidatorInterface;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt;
use function json_decode;
use function sprintf;

use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_USERAGENT;

class RecaptchaValidator implements ValidatorInterface
{
    private const RECAPTCHA_VERIFICATION_URI_PATTERN =
        'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s';

    private array $messages = [];

    public function __construct(private string $key)
    {
    }

    /**
     * @param mixed $value
     */
    public function isValid($value): bool
    {
        $json     = $this->sendRecaptchaVerification($value);
        $response = json_decode($json);
        if (! isset($response->success) || $response->success !== true) {
            $this->messages = ['ReCaptcha was invalid!'];
            return false;
        }
        return true;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    private function sendRecaptchaVerification(string $value): string
    {
        $ch = curl_init(sprintf(self::RECAPTCHA_VERIFICATION_URI_PATTERN, $this->key, $value));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'mwopnet/recaptcha-validator');

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}
