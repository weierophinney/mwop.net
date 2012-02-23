<?php
$config = array(
    'view' => array(
        'search' => array(
            'api_key' => 'GOOGLE_SEARCH_KEY_GOES_HERE',
        )
    ),
    'di' => array('instance' => array(
        'Zend\Mvc\View\ExceptionStrategy' => array('parameters' => array(
                'displayExceptions' => false,
            ),
        ),
        'Application\View\Helper\Disqus' => array('parameters' => array(
            'options' => array(
                'key'         => 'DISQUS_KEY_GOES_HERE',
                'development' => 0,
            ),
        )),
    )),
);

return $config;
