<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Filter;

use Zend\Validator\AbstractValidator;

class Tags extends AbstractValidator
{
    const INVALID_TAG  = 'tagInvalid';
    const INVALID_TAGS = 'tagsInvalid';

    protected $messageTemplates = [
        self::INVALID_TAG  => 'Invalid tag provided; expected a string, received "%value%".',
        self::INVALID_TAGS => 'Invalid tags provided; expected an array or string, received "%value%".',
    ];

    public function isValid($value) : bool
    {
        $this->setValue($value);
        if (is_array($value)) {
            foreach ($value as $v) {
                if (! is_string($v)) {
                    $this->error(self::INVALID_TAG);
                    return false;
                }
            }
            return true;
        }
        if (is_string($value)) {
            return true;
        }
        $this->error(self::INVALID_TAGS);
        return false;
    }
}
