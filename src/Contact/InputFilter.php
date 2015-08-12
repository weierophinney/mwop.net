<?php
namespace Mwop\Contact;

use Zend\InputFilter\InputFilter as BaseInputFilter;
use Zend\Validator\Hostname as HostnameValidator;

class InputFilter extends BaseInputFilter
{
    public function __construct()
    {
        $this->add(array(
            'name'       => 'from',
            'required'   => true,
            'validators' => array(
                array(
                    'name'    => 'Zend\Validator\EmailAddress',
                    'options' => array(
                        'allow'  => HostnameValidator::ALLOW_DNS,
                        'domain' => true,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'subject',
            'required'   => true,
            'filters'    => array(
                array(
                    'name'    => 'Zend\Filter\StripTags',
                ),
            ),
            'validators' => array(
                array(
                    'name'    => 'Zend\Validator\StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 2,
                        'max'      => 140,
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name'       => 'body',
            'required'   => true,
        ));
    }
}
