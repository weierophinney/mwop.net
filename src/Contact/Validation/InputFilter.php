<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Contact\Validation;

use Laminas\InputFilter\InputFilter as BaseInputFilter;
use Laminas\Validator\Hostname as HostnameValidator;

class InputFilter extends BaseInputFilter
{
    public function __construct(string $reCaptchaKey)
    {
        $this->add([
            'name'       => 'from',
            'required'   => true,
            'validators' => [
                [
                    'name'    => 'Laminas\Validator\EmailAddress',
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
                    'name'    => 'Laminas\Filter\StripTags',
                ],
            ],
            'validators' => [
                [
                    'name'    => 'Laminas\Validator\StringLength',
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
            'name'       => 'body',
            'required'   => true,
        ]);
    }
}
