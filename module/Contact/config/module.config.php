<?php
$config = array(
'di' => array(
    'definition' => array('class' => array(
        'Contact\Controller\ContactController' => array(
            'setContactForm' => array(
                'form' => array(
                    'required' => true,
                    'type'     => 'Contact\Form\ContactForm',
                ),
            ),
        ),
        'Contact\Form\ContactForm' => array(
            '__construct' => array(
                'options' => array(
                    'required' => true,
                    'type'     => 'Zend\Captcha\ReCaptcha',
                ),
            ),
        ),
        'Zend\Captcha\ReCaptcha' => array(
            'setPrivkey' => array(
                'privkey' => array(
                    'required' => true,
                    'type'     => false,
                ),
            ),
            'setPubkey' => array(
                'pubkey' => array(
                    'required' => true,
                    'type'     => false,
                ),
            ),
            'setOption' => array(
                'key' => array(
                    'required' => false,
                    'type'     => false,
                ),
                'value' => array(
                    'required' => false,
                    'type'     => false,
                ),
            ),
        ),
        'Zend\Mail\Message' => array(
            'addTo' => array(
                'emailOrAddressList' => array(
                    'type' => false,
                    'required' => true,
                ),
                'name' => array(
                    'type' => false,
                    'required' => false,
                ),
            ),
            'addFrom' => array(
                'emailOrAddressList' => array(
                    'type' => false,
                    'required' => true,
                ),
                'name' => array(
                    'type' => false,
                    'required' => false,
                ),
            ),
            'setSender' => array(
                'emailOrAddressList' => array(
                    'type' => false,
                    'required' => true,
                ),
                'name' => array(
                    'type' => false,
                    'required' => false,
                ),
            ),
        ),
    )),
    'instance' => array(
        'alias' => array(
            'contact-contact' => 'Contact\Controller\ContactController',
            'view'            => 'Zend\View\PhpRenderer',
            'view-resolver'   => 'Zend\View\TemplatePathStack',
        ),

        'Zend\Mail\Message' => array('parameters' => array(
            'Zend\Mail\Message::addTo:emailOrAddressList' => 'EMAIL HERE',
            'Zend\Mail\Message::addTo:name'  => "NAME HERE",
        )),

        'view' => array('parameters' => array(
            'resolver' => 'view-resolver',
        )),

        'view-resolver' => array('parameters' => array(
            'paths' => array(
                'contact' => __DIR__ . '/../view',
            ),
        )),

        'Contact\Controller\ContactController' => array('parameters' => array(
            'message'   => 'Zend\Mail\Message',
            'form'      => 'Contact\Form\ContactForm',
        )),

        'Contact\Form\ContactForm' => array('parameters' => array(
            'recaptcha' => 'Zend\Captcha\ReCaptcha',
        )),

        'Zend\Captcha\ReCaptcha' => array('parameters' => array(
            'pubkey'  => 'RECAPTCHA_PUBKEY_HERE',
            'privkey' => 'RECAPTCHA_PRIVKEY_HERE',
        )),
    ),
),

'routes' => array(
    'contact-form' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/contact',
            'defaults' => array(
                'controller' => 'contact-contact',
                'action'     => 'index',
            ),
        ),
    ),

    'contact-process' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/contact/process',
            'defaults' => array(
                'controller' => 'contact-contact',
                'action'     => 'process',
            ),
        ),
    ),

    'contact-thank-you' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/contact/thank-you',
            'defaults' => array(
                'controller' => 'contact-contact',
                'action'     => 'thank-you',
            ),
        ),
    ),
),
);
return $config;
