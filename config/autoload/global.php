<?php
return array(
    'phly-comic' => array(
        'output_path'     => 'data/',
        'comic_file'      => '%s.html',
        'all_comics_file' => 'comics.html',
    ),
    'service_manager' => array(
        'invokables' => array(
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ),
    ),
);
