<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Implements Content-Security-Policy header for site.
 */
class ContentSecurityPolicy
{
    public function __invoke(Request $req, Response $res, callable $next) : Response
    {
        $res = $next($req, $res);

        return $res
            ->withHeader('Content-Security-Policy', $this->createContentSecurityPolicy())
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block');
    }

    private function createContentSecurityPolicy() : string
    {
        $policies = [
            'default-src' => [
                "'self'",
            ],
            'child-src' => [
                "'self'",
                'https://www.google.com',
                'https://screencasts.mwop.net',
                'https://vimeo.com',
                'https://youtube.com',
                'disqus.com',
            ],
            'font-src' => [
                "'self'",
                'https:',
            ],
            'img-src' => [
                "'self'",
                'data:',
                'http:',
                'https:',
            ],
            'script-src' => [
                "'self'",
                'data:',
                'https://cdn.ampproject.org',
                'https://www.google.com',
                'https://www.google-analytics.com',
                'https://www.gstatic.com',
                'https://code.jquery.com',
                '*.disqus.com',
                '*.disquscdn.com',
                'https://platform.twitter.com',
                'https://*.twimg.com',
            ],
            'style-src' => [
                "'self'",
                "'unsafe-inline'", // allow inlined styles; mostly for widgets
                'https://fonts.googleapis.com',
                'platform.twitter.com',
                'https://*.twimg.com',
                '*.disqus.com',
                '*.disquscdn.com',
            ],
        ];

        array_walk($policies, function (&$value, $key) {
            $value = sprintf('%s %s', $key, implode(' ', $value));
        });

        return implode(' ; ', $policies);
    }
}
