<?php
namespace Mwop\Blog\Filter;

use Mwop\Blog\Filter\Timezone as TimezoneValidator;
use Zend\InputFilter\InputFilter;

class EntryFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'id',
            'filters' => [
                ['name' => 'string_trim']
            ],
            'required' => true,
        ]);

        $this->add([
            'name' => 'title',
            'filters' => [
                ['name' => 'string_trim'],
                ['name' => 'strip_tags'],
            ],
            'validators' => [
                [
                    'name' => 'string_length',
                    'options' => [
                        'min' => 3,
                    ],
                ]
            ],
            'required' => true,
        ]);

        $this->add([
            'name' => 'body',
            'filters' => [
                ['name' => 'string_trim'],
            ],
            'required'    => false,
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'extended',
            'filters' => [
                ['name' => 'string_trim'],
            ],
            'required'    => false,
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'author',
            'filters' => [
                function ($value) {
                    if (is_array($value) || is_object($value)) {
                        return $value;
                    }
                    return trim($value);
                },
            ],
            'validators' => [
                new AuthorIsValid(),
            ],
            'required'    => true,
            'allow_empty' => false,
        ]);

        $this->add([
            'name' => 'created',
            'validators' => [
                ['name' => 'int'],
            ],
            'required'    => false,
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'updated',
            'validators' => [
                ['name' => 'int'],
            ],
            'required'    => false,
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'is_draft',
            'filters' => [
                ['name' => 'boolean'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [true, false],
                        'strict'   => true,
                    ],
                ],
            ],
            'required'    => false,
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'is_public',
            'filters' => [
                ['name' => 'boolean'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [true, false],
                        'strict'   => true,
                    ],
                ],
            ],
            'required'    => false,
            'allow_empty' => true,
        ]);

        $this->add([
            'name' => 'timezone',
            'filters' => [
                ['name' => 'string_trim'],
            ],
            'validators' => [
                new TimezoneValidator(),
            ],
            'required' => true,
        ]);

        $this->add([
            'name' => 'tags',
            'validators' => [
                new Tags(),
            ],
            'required' => false,
            'allow_empty' => true,
        ]);
    }
}
