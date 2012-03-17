<?php
return array('di' => array(
    'instance' => array(
        'Zend\Mail\Message' => array('parameters' => array(
            'Zend\Mail\Message::addTo:emailOrAddressList'     => 'EMAIL HERE',
            'Zend\Mail\Message::setSender:emailOrAddressList' => 'EMAIL HERE',
        )),

        'Zend\Mail\Transport\Smtp' => array('parameters' => array(
            'options' => 'Zend\Mail\Transport\SmtpOptions',
        )),


        // This is how to configure using GMail as your SMTP server
        'Zend\Mail\Transport\SmtpOptions' => array('parameters' => array(
            'host' => 'HOSTNAME HERE',
        )),

        'PhlyContact\Controller\ContactController' => array('parameters' => array(
            'transport' => 'Zend\Mail\Transport\Smtp',
        )),

        'PhlyContact\Form\ContactForm' => array('parameters' => array(
            'captchaAdapter' => 'Zend\Captcha\ReCaptcha',
        )),

        'Zend\Captcha\ReCaptcha' => array('parameters' => array(
            'Zend\Captcha\ReCaptcha::setOption:key'   => 'theme',                                                                                              
            'Zend\Captcha\ReCaptcha::setOption:value' => 'clean',
        )),
    ),
));

