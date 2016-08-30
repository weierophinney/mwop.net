<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Mni\FrontYAML\Parser;
use Mwop\Blog\MarkdownFileFilter;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use ZF\Console\Route;

class GenerateSearchData
{
    public function __invoke(Route $route, Console $console) : int
    {
        $basePath = $route->getMatchedParam('path');
        $path     = realpath($basePath) . '/data/blog';

        $console->writeLine('Generating search metadata', Color::BLUE);

        $documents = [];
        $parser = new Parser();
        foreach (new MarkdownFileFilter($path) as $fileInfo) {
            $document = $parser->parse(file_get_contents($fileInfo->getPathname()), false);
            $metadata = $document->getYAML();
            $content  = $document->getContent();

            $documents[] = [
                'id'      => sprintf('/blog/%s.html', $metadata['id']),
                'tags'    => implode(' ', $metadata['tags']),
                'title'   => $metadata['title'],
                'content' => $content,
            ];
        }

        file_put_contents(
            realpath($basePath) . '/data/search_terms.json',
            json_encode(['docs' => $documents], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
        $console->write('[DONE]', Color::GREEN);
        $console->writeLine(' Generating search metadata');

        return 0;
    }
}
