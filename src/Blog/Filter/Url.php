<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Filter;

use Zend\Uri\Uri;
use Zend\Uri\UriFactory;
use Zend\Validator\AbstractValidator;

class Url extends AbstractValidator
{
    const INVALID_URL  = 'urlInvalid';

    protected $messageTemplates = [
        self::INVALID_URL  => 'Invalid url provided; received "%value%".',
    ];

    public function isValid($value) : bool
    {
        $this->setValue($value);

        if (! $value instanceof Uri) {
            $value = UriFactory::factory($value);
        }

        if (! $value->isValid()) {
            $this->error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}
