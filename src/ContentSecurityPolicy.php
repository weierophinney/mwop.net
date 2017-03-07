<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Implements Content-Security-Policy header for site.
 */
class ContentSecurityPolicy implements MiddlewareInterface
{
    /**
     * @return Response
     */
    public function process(Request $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);

        return $response
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
            'connect-src' => [
                "'self'",
                'https:',
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
