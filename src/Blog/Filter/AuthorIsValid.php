<?php
namespace Mwop\Blog\Filter;

use Mwop\Blog\AuthorEntity;
use Zend\Validator\AbstractValidator;

class AuthorIsValid extends AbstractValidator
{
    const INVALID_AUTHOR         = 'authorInvalid';
    const INVALID_NAME           = 'authorNameInvalid';
    const INVALID_NAME_TOO_SHORT = 'authorNameTooShort';
    const INVALID_TYPE           = 'authorTypeInvalid';

    protected $messageTemplates = array(
        self::INVALID_AUTHOR         => 'Invalid author provided',
        self::INVALID_NAME           => 'Author name must be 1 alphabetic character followed by 0 or more alphanumeric, dash, or underscore characters',
        self::INVALID_NAME_TOO_SHORT => 'Author name must be at least 1 character',
        self::INVALID_TYPE           => 'Invalid author type provided',
    );

    public function isValid($value)
    {
        $this->setValue($value);

        if (is_string($value)) {
            if (strlen($value) < 1) {
                $this->error(self::INVALID_NAME_TOO_SHORT);
                return false;
            }
            if (!preg_match('/[a-z][a-z0-9_-]*/i', $value)) {
                $this->error(self::INVALID_NAME);
                return false;
            }
            return true;
        }

        if (is_array($value)) {
            $author = new AuthorEntity();
            $author->exchangeArray($value);
            $value = $author;
            unset($author);
        }

        if (!$value instanceof AuthorEntity) {
            $this->error(self::INVALID_TYPE);
            return false;
        }

        if (!$value->isValid()) {
            $this->error(self::INVALID_AUTHOR);
            return false;
        }

        return true;
    }
}
