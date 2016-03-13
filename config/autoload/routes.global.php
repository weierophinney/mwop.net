<?php
use Mwop\Blog;
use Mwop\ComicsPage;
use Mwop\Contact;
use Mwop\Factory;
use Mwop\HomePage;
use Mwop\Job;
use Mwop\ResumePage;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\FastRouteRouter;

return [
    'dependencies' => [
        'delegators' => [
            Blog\DisplayPostMiddleware::class => [
                Blog\CachingDelegatorFactory::class,
            ],
        ],
        'invokables' => [
            Blog\FeedMiddleware::class           => Blog\FeedMiddleware::class,
            Blog\Console\SeedBlogDatabase::class => Blog\Console\SeedBlogDatabase::class,
            BodyParamsMiddleware::class          => BodyParamsMiddleware::class,
            RouterInterface::class               => FastRouteRouter::class,
        ],
        'factories' => [
            Blog\DisplayPostMiddleware::class => Blog\DisplayPostMiddlewareFactory::class,
            Blog\ListPostsMiddleware::class   => Blog\ListPostsMiddlewareFactory::class,
            Contact\LandingPage::class        => Contact\LandingPageFactory::class,
            Contact\Process::class            => Contact\ProcessFactory::class,
            Contact\ThankYouPage::class       => Contact\ThankYouPageFactory::class,
            ComicsPage::class                 => Factory\ComicsPage::class,
            HomePage::class                   => Factory\PageFactory::class,
            Job\GithubFeed::class             => Job\GithubFeedFactory::class,
            ResumePage::class                 => Factory\PageFactory::class,
            'Mwop\OfflinePage'                => Factory\PageFactory::class,
        ],
    ],

    'routes' => [
        [
            'path'            => '/',
            'middleware'      => HomePage::class,
            'allowed_methods' => ['GET'],
            'name'            => 'home',
        ],
        [
            'path'            => '/comics',
            'middleware'      => ComicsPage::class,
            'allowed_methods' => ['GET'],
            'name'            => 'comics',
        ],
        [
            'path'            => '/offline',
            'middleware'      => 'Mwop\OfflinePage',
            'allowed_methods' => ['GET'],
            'name'            => 'offline',
        ],
        [
            'path'            => '/resume',
            'middleware'      => ResumePage::class,
            'allowed_methods' => ['GET'],
            'name'            => 'resume',
        ],

        // BLOG

        [
            'path'            => '/blog[/]',
            'middleware'      => Blog\ListPostsMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog',
        ],
        [
            'path'            => '/blog/{id:[^/]+}.html',
            'middleware'      => Blog\DisplayPostMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog.post',
        ],
        [
            'path'            => '/blog/tag/{tag:php}.xml',
            'middleware'      => Blog\FeedMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog.feed.php',
        ],
        [
            'path'            => '/blog/{tag:php}.xml',
            'middleware'      => Blog\FeedMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog.feed.php.also',
        ],
        [
            'path'            => '/blog/tag/{tag:[^/]+}/{type:atom|rss}.xml',
            'middleware'      => Blog\FeedMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog.tag.feed',
        ],
        [
            'path'            => '/blog/tag/{tag:[^/]+}',
            'middleware'      => Blog\ListPostsMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog.tag',
        ],
        [
            'path'            => '/blog/{type:atom|rss}.xml',
            'middleware'      => Blog\FeedMiddleware::class,
            'allowed_methods' => ['GET'],
            'name'            => 'blog.feed',
        ],

        // CONTACT

        [
            'path'            => '/contact[/]',
            'middleware'      => Contact\LandingPage::class,
            'allowed_methods' => ['GET'],
            'name'            => 'contact',
        ],
        [
            'path'            => '/contact/process',
            'middleware'      => [
                BodyParamsMiddleware::class,
                Contact\Process::class,
            ],
            'allowed_methods' => ['POST'],
            'name'            => 'contact.process',
        ],
        [
            'path'            => '/contact/thank-you',
            'middleware'      => Contact\ThankYouPage::class,
            'allowed_methods' => ['GET'],
            'name'            => 'contact.thank-you',
        ],

        // CONTACT

        [
            'path'            => '/jobs/clear-cache',
            'middleware'      => Job\ClearCache::class,
            'allowed_methods' => ['POST'],
            'name'            => 'job.clear-cache',
        ],
        [
            'path'            => '/jobs/comics',
            'middleware'      => Job\Comics::class,
            'allowed_methods' => ['POST'],
            'name'            => 'job.comics',
        ],
        [
            'path'            => '/jobs/github-feed',
            'middleware'      => Job\GithubFeed::class,
            'allowed_methods' => ['POST'],
            'name'            => 'job.github-feed',
        ],
    ],
];
