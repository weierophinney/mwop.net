<?php
namespace Contact\Form;

use Zend\Form\Form,
    Zend\Validator\Hostname as HostnameValidator;

class ContactForm extends Form
{
    public function init()
    {
        $this->setName('contact');
        
        $this->addElement('text', 'from', array(
            'label'     => 'From:',
            'required'  => true,
            'validators' => array(
                array('EmailAddress', true, array(
                    'allow'  => HostnameValidator::ALLOW_DNS,
                    'domain' => true,
                )),
            ),
        ));

        $this->addElement('text', 'subject', array(
            'label'      => 'Subject:',
            'required'   => true,
            'filters'    => array(
                'StripTags',
            ),
            'validators' => array(
                array('StringLength', true, array(
                    'encoding' => 'UTF-8',
                    'min'      => 2,
                    'max'      => 140,
                )),
            ),
        ));

        $this->addElement('textarea', 'body', array(
            'label'    => 'Your message:',
            'required' => true,
        ));

        $this->addElement('captcha', 'captcha', array(
            'label'          => 'Please verify you are human.',
            'required'       => true,
            'captcha'        => 'Figlet',
            'captchaOptions' => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300,
            ),
        ));

        $this->addElement('hash', 'csrf', array(
            'ignore'   => true,
            'required' => true,
        ));

        $this->addElement('submit', 'Send', array(
            'label'    => 'Send',
            'required' => false,
            'ignore'   => true,
        ));
    }
}
