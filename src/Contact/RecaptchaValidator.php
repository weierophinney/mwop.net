<?php
namespace Mwop\Contact;

use Zend\Validator\ValidatorInterface;

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
        $uri  = sprintf(self::RECAPTCHA_VERIFICATION_URI_PATTERN, $this->key, $value);
        $json = file_get_contents($uri);
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
}
