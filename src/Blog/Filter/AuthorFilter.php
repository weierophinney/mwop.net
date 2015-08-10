<?php
namespace Mwop\Blog\Filter;

use Zend\InputFilter\InputFilter;

class AuthorFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name' => 'id',
            'filters' => array(
                array('name' => 'string_trim'),
            ),
            'validators' => array(
                new AuthorIsValid(),
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'name',
            'filters' => array(
                array('name' => 'string_trim'),
                array('name' => 'strip_tags'),
            ),
            'validators' => array(
                array(
                    'name' => 'string_length',
                    'options' => array(
                        'min' => 1,
                    ),
                ),
            ),
            'required' => true,
        ));

        $this->add(array(
            'name' => 'email',
            'filters' => array(
                array('name' => 'string_trim'),
            ),
            'validators' => array(
                array('name' => 'emailaddress'),
            ),
            'allow_empty' => true,
        ));

        $this->add(array(
            'name' => 'url',
            'filters' => array(
                array('name' => 'string_trim'),
            ),
            'validators' => array(
                new Url(),
            ),
            'allow_empty' => true,
        ));
    }
}
