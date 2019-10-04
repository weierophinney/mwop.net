<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use DOMDocument;
use JsonException;

use function file_get_contents;
use function fwrite;
use function json_decode;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function preg_match;
use function preg_replace;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;

use const E_WARNING;
use const JSON_THROW_ON_ERROR;
use const LIBXML_HTML_NODEFDTD;
use const STDERR;

class InstagramClient
{
    /** @var bool */
    private $debug;

    /** @var string */
    private $url;

    public function __construct(string $url, bool $debug = false)
    {
        $this->url   = $url;
        $this->debug = $debug;
    }

    /**
     * @return array<string, string>
     */
    public function fetchFeed() : array
    {
        $feed = [];
        $html = $this->fetchHtml();
        $json = $this->parseHtml($html);

        foreach ($this->parseJsonForItems($json) as $item) {
            $node = $item['node'] ?? [];
            if (! (isset($node['thumbnail_resources']) && is_array($node['thumbnail_resources']))
                || ! isset($node['shortcode'])
            ) {
                continue;
            }

            $thumbnail = array_shift($node['thumbnail_resources']);
            if (! isset($thumbnail['src'])) {
                continue;
            }

            $feed[] = [
                'post_url'  => sprintf('https://www.instagram.com/p/%s', $node['shortcode']),
                'image_url' => $thumbnail['src'],
            ];
        }

        return $feed;
    }

    private function fetchHtml() : string
    {
        if ('' === $this->url) {
            if ($this->debug) {
                fwrite(STDERR, 'No Instagram URL is configured');
            }
            return '';
        }

        set_error_handler(function ($errno, $errstr) {
            if ($errno !== E_WARNING) {
                return false;
            }

            if ($this->debug) {
                fwrite(STDERR, sprintf('Error fetching Instagram page (%s): %s', $this->url, $errstr));
            }
        });
        $html = file_get_contents($this->url);
        restore_error_handler();

        return false === $html ? '' : $html;
    }

    /**
     * @return string Literal JSON discovered in the HTML document.
     */
    private function parseHtml(string $html) : string
    {
        $dom  = new DOMDocument();
        $dom->strictErrorChecking = false;
        $useLibxmlErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($useLibxmlErrors);

        foreach ($dom->getElementsByTagName('script') as $node) {
            if (! $node->hasAttributes() || count($node->attributes) > 1) {
                continue;
            }

            if (! preg_match('/^window\._sharedData/', $node->textContent)) {
                continue;
            }

            return preg_replace('/^window\._sharedData\s*\=[^{]*(\{.*});$/s', '$1', $node->textContent);
        }

        return '{}';
    }

    /**
     * @return array[] Array of arrays.
     */
    private function parseJsonForItems(string $json) : array
    {
        try {
            $query = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            fwrite(STDERR, sprintf(
                "Error parsing data from Instagram page (%s): %s\nJSON: %s",
                $this->url,
                $e->getMessage(),
                $json
            ));
            return [];
        }

        return $query['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ?? [];
    }
}
