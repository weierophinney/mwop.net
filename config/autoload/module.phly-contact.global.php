<?php
return array(
    'phly_contact' => array(
        'captcha' => array(
            'class'   => 'recaptcha',
            'options' => array(
                'pubkey'  => 'RECAPTCHA_PUBKEY_HERE',
                'privkey' => 'RECAPTCHA_PRIVKEY_HERE',
                'theme'   => 'clean',
            ),
        ),

        'message' => array(
            // These can be either a string, or an array of email => name pairs
            /*
            'to'     => 'contact@your.tld',
            'sender' => 'contact@your.tld',
             */
        ),

        'mail_transport' => array(
            'class'   => 'Zend\Mail\Transport\Smtp',
            'options' => array(
                'host' => 'HOSTNAME HERE'
            ),
        ),
    ),
);
