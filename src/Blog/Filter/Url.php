<?php
namespace Mwop\Blog\Filter;

use Zend\Uri\Uri;
use Zend\Uri\UriFactory;
use Zend\Validator\AbstractValidator;

class Url extends AbstractValidator
{
    const INVALID_URL  = 'urlInvalid';

    protected $messageTemplates = array(
        self::INVALID_URL  => 'Invalid url provided; received "%value%".',
    );

    public function isValid($value)
    {
        $this->setValue($value);

        if (!$value instanceof Uri) {
            $value = UriFactory::factory($value);
        }

        if (!$value->isValid()) {
            $this->error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}
