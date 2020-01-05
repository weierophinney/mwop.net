<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Validation;

use Laminas\Validator\ValidatorInterface;

class RecaptchaValidator implements ValidatorInterface
{
    const RECAPTCHA_VERIFICATION_URI_PATTERN =
        'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s';

    private $key;

    private $messages = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function isValid($value) : bool
    {
        $json     = $this->sendRecaptchaVerification($value);
        $response = json_decode($json);
        if (! isset($response->success) || $response->success !== true) {
            $this->messages = [ 'ReCaptcha was invalid!' ];
            return false;
        }
        return true;
    }

    public function getMessages() : array
    {
        return $this->messages;
    }

    private function sendRecaptchaVerification(string $value) : string
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
