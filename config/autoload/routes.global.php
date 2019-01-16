<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

use Mwop\ComicsPage;
use Mwop\Contact;
use Mwop\Factory;
use Mwop\HomePage;
use Mwop\ResumePage;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

return [
    'dependencies' => [
        'invokables' => [
            BodyParamsMiddleware::class          => BodyParamsMiddleware::class,
            RouterInterface::class               => FastRouteRouter::class,
        ],
        'factories' => [
            Contact\LandingPage::class        => Contact\LandingPageFactory::class,
            Contact\Process::class            => Contact\ProcessFactory::class,
            Contact\ThankYouPage::class       => Contact\ThankYouPageFactory::class,
            ComicsPage::class                 => Factory\ComicsPage::class,
            HomePage::class                   => Factory\HomePageFactory::class,
            ResumePage::class                 => Factory\PageFactory::class,
            'Mwop\OfflinePage'                => Factory\PageFactory::class,
        ],
    ],
];
