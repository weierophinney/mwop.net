<?php

declare(strict_types=1);

namespace Mwop\Contact\Validation;

use Laminas\Filter\StripTags;
use Laminas\InputFilter\InputFilter as BaseInputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Hostname as HostnameValidator;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;

class InputFilter extends BaseInputFilter
{
    public function __construct(string $reCaptchaKey)
    {
        $this->add([
            'name'       => 'from',
            'required'   => true,
            'validators' => [
                [
                    'name'    => EmailAddress::class,
                    'options' => [
                        'allow'  => HostnameValidator::ALLOW_DNS,
                        'domain' => true,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'subject',
            'required'   => true,
            'filters'    => [
                [
                    'name' => StripTags::class,
                ],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 2,
                        'max'      => 140,
                    ],
                ],
            ],
        ]);

        $this->add([
            'name'       => 'g-recaptcha-response',
            'required'   => true,
            'validators' => [
                new RecaptchaValidator($reCaptchaKey),
            ],
        ]);

        $this->add([
            'name'          => 'body',
            'required'      => true,
            'validators'    => [
                [
                    'name'    => Regex::class,
                    'options' => [
                        'pattern' => '/^((?!talk with web).)*$/is',
                    ],
                ],
            ],
            'error_message' => 'Your contact message was either empty, or contains disallowed content.',
        ]);
    }
}
