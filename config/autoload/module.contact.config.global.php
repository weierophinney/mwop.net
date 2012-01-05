<?php
$config = array(
'di' => array(
    'preferences' => array(
        'Zend\Mail\AbstractTransport' => 'Zend\Mail\Transport\Smtp',
    ),

    'instance' => array(
        'Zend\Mail\Mail' => array('parameters' => array(
            'Zend\Mail\Mail::addTo:email' => 'EMAIL HERE',
            'Zend\Mail\Mail::addTo:name'  => "NAME HERE",
        )),

        'Zend\Mail\Transport\Smtp' => array('parameters' => array(
            'host'   => 'HOSTNAME HERE',
            'config' => array( /* options here */ ),
        )),

        'Zend\Captcha\ReCaptcha' => array('parameters' => array(
            'pubkey'  => 'RECAPTCHA_PUBKEY_HERE',
            'privkey' => 'RECAPTCHA_PRIVKEY_HERE',
        )),

        'Contact\Controller\ContactController' => array('parameters' => array(
            'transport' => 'Zend\Mail\Transport\Smtp',
        )),
    ),
),
);
return $config;
