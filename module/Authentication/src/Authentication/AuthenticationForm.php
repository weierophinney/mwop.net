<?php

namespace Authentication;

use Zend\Form\Form;

class AuthenticationForm extends Form
{
    public function init()
    {
        $this->addElement('text', 'username', array(
            'label'    => 'Username:',
            'required' => true,
            'validators' => array(
                'Alnum',
                array('StringLength', true, array('min' => 3)),
            ),
        ));
        $this->addElement('password', 'password', array(
            'label'    => 'Password',
            'required' => true,
            'validators' => array(
                array('StringLength', true, array('min' => 3)),
            ),
        ));
        $this->addElement('submit', 'login', array(
            'label'    => 'Login',
            'required' => 'false',
            'ignore'   => true,
        ));
    }
}
