<?php

declare(strict_types=1);

use Mwop\Blog\Console\ClearCache as BlogClearCache;
use Mwop\Blog\Console\FeedGenerator;
use Mwop\Blog\Console\GenerateSearchData;
use Mwop\Blog\Console\SeedBlogDatabase;
use Mwop\Blog\Console\TagCloud;
use Mwop\Console\ClearCache;
use Mwop\Console\FeedAggregator;
use Mwop\Github\Console\Fetch;

return [
    'laminas-cli' => [
        'commands' => [
            'blog:clear-cache'          => BlogClearCache::class,
            'blog:feed-generator'       => FeedGenerator::class,
            'blog:generate-search-data' => GenerateSearchData::class,
            'blog:seed-db'              => SeedBlogDatabase::class,
            'blog:tag-cloud'            => TagCloud::class,
            'clear-cache'               => ClearCache::class,
            'github:fetch-activity'     => Fetch::class,
            'homepage-feeds'            => FeedAggregator::class,
        ],
    ],
];
