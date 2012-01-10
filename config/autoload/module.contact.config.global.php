<?php
$config = array(
'di' => array(
    'preferences' => array(
        'Zend\Mail\Transport' => 'Zend\Mail\Transport\Smtp',
    ),

    'instance' => array(
        'Zend\Mail\Message' => array('parameters' => array(
            'Zend\Mail\Message::addTo:emailOrAddressList' => 'EMAIL HERE',
            // 'Zend\Mail\Message::addTo:name'  => "NAME HERE",
            'Zend\Mail\Message::setSender:emailOrAddressList' => 'EMAIL HERE',
        )),

        'Zend\Mail\Transport\Smtp' => array('parameters' => array(
            'options' => 'Zend\Mail\Transport\SmtpOptions',
        )),

        'Zend\Mail\Transport\SmtpOptions' => array('parameters' => array(
            'host'   => 'HOSTNAME HERE',
            /* options here */
        )),

        'Zend\Captcha\ReCaptcha' => array('parameters' => array(
            'pubkey'  => 'RECAPTCHA_PUBKEY_HERE',
            'privkey' => 'RECAPTCHA_PRIVKEY_HERE',
            'Zend\Captcha\ReCaptcha::setOption:key'   => 'theme',
            'Zend\Captcha\ReCaptcha::setOption:value' => 'clean',
        )),

        'Contact\Controller\ContactController' => array('parameters' => array(
            'transport' => 'Zend\Mail\Transport\Smtp',
        )),
    ),
),
);
return $config;
