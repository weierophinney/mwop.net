<?php
namespace Mwop\Blog\Filter;

use Zend\Validator\AbstractValidator;

class Timezone extends AbstractValidator
{
    const INVALID_TIMEZONE = 'timezoneInvalid';

    protected $messageTemplates = [
        self::INVALID_TIMEZONE  => 'Invalid timezone "%value%" provided.',
    ];

    public function isValid(string $value) : bool
    {
        $this->setValue($value);
        if (! timezone_open($value)) {
            $this->error(self::INVALID_TIMEZONE);
            return false;
        }
        return true;
    }
}
