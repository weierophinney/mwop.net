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
        'Zend\Mail\Message' => array('parameters' => array(
            'Zend\Mail\Message::addTo:emailOrAddressList' => 'EMAIL HERE',
            'Zend\Mail\Message::addTo:name'  => "NAME HERE",
        )),

        'Zend\View\Resolver\TemplateMapResolver' => array('parameters' => array(
            'map' => array(
                'contact/index'     => __DIR__ . '/../view/contact/index.phtml',
                'contact/thank-you' => __DIR__ . '/../view/contact/thank-you.phtml',
            ),
        )),

        'Zend\View\Resolver\TemplatePathStack' => array('parameters' => array(
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
        
        'Zend\Mvc\Router\RouteStack' => array('parameters' => array(                                          
            'routes' => array(
                'contact-form' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/contact',
                        'defaults' => array(
                            'controller' => 'Contact\Controller\ContactController',
                            'action'     => 'index',
                        ),
                    ),
                ),

                'contact-process' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/contact/process',
                        'defaults' => array(
                            'controller' => 'Contact\Controller\ContactController',
                            'action'     => 'process',
                        ),
                    ),
                ),

                'contact-thank-you' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/contact/thank-you',
                        'defaults' => array(
                            'controller' => 'Contact\Controller\ContactController',
                            'action'     => 'thank-you',
                        ),
                    ),
                ),
            ),
        )),
    ),
),
);
return $config;
