<?php
namespace Mwop\Blog\Filter;

use Mwop\Blog\Filter\Timezone as TimezoneValidator;
use Zend\InputFilter\InputFilter;

class EntryFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'id',
            'filters' => array(
                array('name' => 'string_trim')
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'title',
            'filters' => array(
                array('name' => 'string_trim'),
                array('name' => 'strip_tags'),
            ),
            'validators' => array(
                array(
                    'name' => 'string_length',
                    'options' => array(
                        'min' => 3,
                    ),
                )
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'body',
            'filters' => array(
                array('name' => 'string_trim'),
            ),
            'required'    => false,
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'extended',
            'filters' => array(
                array('name' => 'string_trim'),
            ),
            'required'    => false,
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'author',
            'filters' => array(
                function($value) {
                    if (is_array($value) || is_object($value)) {
                        return $value;
                    }
                    return trim($value);
                },
            ),
            'validators' => array(
                new AuthorIsValid(),
            ),
            'required'    => true,
            'allow_empty' => false,
        ));

        $this->add(array(
            'name' => 'created',
            'validators' => array(
                array('name' => 'int'),
            ),
            'required'    => false,
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'updated',
            'validators' => array(
                array('name' => 'int'),
            ),
            'required'    => false,
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'is_draft',
            'filters' => array(
                array('name' => 'boolean'),
            ),
            'validators' => array(
                array(
                    'name' => 'InArray',
                    'options' => array(
                        'haystack' => array(true, false),
                        'strict'   => true,
                    ),
                ),
            ),
            'required'    => false,
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'is_public',
            'filters' => array(
                array('name' => 'boolean'),
            ),
            'validators' => array(
                array(
                    'name' => 'InArray',
                    'options' => array(
                        'haystack' => array(true, false),
                        'strict'   => true,
                    ),
                ),
            ),
            'required'    => false,
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'timezone',
            'filters' => array(
                array('name' => 'string_trim'),
            ),
            'validators' => array(
                new TimezoneValidator(),
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'tags',
            'validators' => array(
                new Tags(),
            ),
            'required' => false,
            'allow_empty' => true,
        ));
    }
}
