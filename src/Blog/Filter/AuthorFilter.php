<?php
namespace Mwop\Blog\Filter;

use Zend\InputFilter\InputFilter;

class AuthorFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'id',
            'filters' => [
                ['name' => 'string_trim'],
            ],
            'validators' => [
                new AuthorIsValid(),
            ],
            'required' => true,
        ]);

        $this->add([
            'name' => 'name',
            'filters' => [
                ['name' => 'string_trim'],
                ['name' => 'strip_tags'],
            ],
            'validators' => [
                [
                    'name' => 'string_length',
                    'options' => [
                        'min' => 1,
                    ],
                ],
            ],
            'required' => true,
        ]);

        $this->add([
            'name' => 'email',
            'filters' => [
                ['name' => 'string_trim'],
            ],
            'validators' => [
                ['name' => 'emailaddress'],
            ],
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'url',
            'filters' => [
                ['name' => 'string_trim'],
            ],
            'validators' => [
                new Url(),
            ],
            'allow_empty' => true,
        ]);
    }
}
