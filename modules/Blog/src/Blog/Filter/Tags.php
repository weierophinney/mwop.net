<?php
namespace Blog\Filter;

use Zend\Validator\AbstractValidator;

class Tags extends AbstractValidator
{
    const INVALID_TAG  = 'tagInvalid';
    const INVALID_TAGS = 'tagsInvalid';

    protected $_messageTemplates = array(
        self::INVALID_TAG  => 'Invalid tag provided; expected a string, received "%value%".',
        self::INVALID_TAGS => 'Invalid tags provided; expected an array or string, received "%value%".',
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        if (is_array($value)) {
            foreach ($value as $v) {
                if (!is_string($v)) {
                    $this->_error(self::INVALID_TAG);
                    return false;
                }
            }
            return true;
        }
        if (is_string($value)) {
            return true;
        }
        $this->_error(self::INVALID_TAGS);
        return false;
    }
}
