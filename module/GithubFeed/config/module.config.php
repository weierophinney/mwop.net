<?php
return array('di' => array(
    'definition' => array('class' => array(
        'GithubFeed\AtomReader' => array(
            '__construct' => array(
                'required' => true,
                'user' => array(
                    'required' => true,
                    'type'     => false,
                ),
                'token' => array(
                    'required' => true,
                    'type'     => false,
                ),
            ),
            'setLimit' => array(
                'required' => false,
                'limit' => array(
                    'required' => true,
                    'type'     => false,
                ),
            ),
        ),
    )),
));
