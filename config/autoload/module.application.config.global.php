<?php
$config = array(
    'display_exceptions' => false,
    'view' => array(
        'search' => array(
            'api_key' => 'GOOGLE_SEARCH_KEY_GOES_HERE',
        )
    ),
    'di' => array('instance' => array(
        'Application\View\Helper\Disqus' => array('parameters' => array(
            'options' => array(
                'key'         => 'DISQUS_KEY_GOES_HERE',
                'development' => 0,
            ),
        )),
    )),
);

return $config;
