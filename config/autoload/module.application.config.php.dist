<?php
$config = array();
$config['production'] = array(
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

$config['staging']     = $config['production'];
$config['staging']['di']['instance']['Application\View\Helper\Disqus']['parameters']['options']['key'] = "DISQUS_STAGING_KEY_GOES_HERE";
$config['staging']['di']['instance']['Application\View\Helper\Disqus']['parameters']['options']['development'] = 1;

$config['testing']     = $config['production'];
$config['testing']['display_exceptions']    = true;

$config['development'] = $config['production'];
$config['development']['display_exceptions']    = true;
$config['staging']['di']['instance']['Application\View\Helper\Disqus']['parameters']['options']['key'] = "DISQUS_DEV_KEY_GOES_HERE";
$config['development']['di']['instance']['Application\View\Helper\Disqus']['parameters']['options']['development'] = 1;
return $config;
