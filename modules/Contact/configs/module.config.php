<?php
$config['production'] = array(
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
        ),
        'Zend\Mail\Mail' => array(
            'addTo' => array(
                'email' => array(
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

        'Zend\Mail\Mail' => array('parameters' => array(
            'Zend\Mail\Mail::addTo:email' => 'EMAIL HERE',
            'Zend\Mail\Mail::addTo:name'  => "NAME HERE",
        )),

        'view' => array('parameters' => array(
            'resolver' => 'view-resolver',
        )),

        'view-resolver' => array('parameters' => array(
            'paths' => array(
                'contact' => __DIR__ . '/../views',
            ),
        )),

        'Contact\Controller\ContactController' => array('parameters' => array(
            'mailer'    => 'Zend\Mail\Mail',
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
$config['staging']     = $config['production'];
$config['testing']     = $config['production'];
$config['development'] = $config['production'];
return $config;

