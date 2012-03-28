<?php
namespace PhlyCommon\Filter;

use Zend\Validator\AbstractValidator;

class Timezone extends AbstractValidator
{
    const INVALID_TIMEZONE = 'timezoneInvalid';

    protected $_messageTemplates = array(
        self::INVALID_TIMEZONE  => 'Invalid timezone "%value%" provided.',
    );

    public function isValid($value)
    {
        $this->setValue($value);
        if (!timezone_open($value)) {
            $this->error(self::INVALID_TIMEZONE);
            return false;
        }
        return true;
    }
}

