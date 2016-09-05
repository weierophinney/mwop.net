<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Github\Console;

use Mwop\Github;
use Throwable;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Escaper\Escaper;
use ZF\Console\Route;

/**
 * Fetch github user activity links
 */
class Fetch
{
    /**
     * @var string
     */
    private $outputTemplateString = '<li><a href="%s">%s</a></li>';

    /**
     * @var Github\AtomReader
     */
    private $reader;

    public function __construct(Github\AtomReader $reader = null, string $outputTemplateString = '')
    {
        $this->reader = $reader;
        if (! empty($outputTemplateString)) {
            $this->outputTemplateString = $outputTemplateString;
        }
    }

    /**
     * Handle the incoming console request
     */
    public function __invoke(Route $route, Console $console) : int
    {
        if (! $route->matchedParam('output')) {
            return $this->reportError($console, $width, $length, 'Missing output file');
        }

        $message = 'Retrieving Github activity links';
        $length  = strlen($message);
        $width   = $console->getWidth();
        $console->write($message, Color::BLUE);

        try {
            $data = $this->reader->read();
        } catch (Throwable $e) {
            return $this->reportError($console, $width, $length, $e);
        }

        file_put_contents(
            $route->getMatchedParam('output'),
            $this->createContentFromData(
                $data,
                $route->getMatchedParam(
                    'template',
                    $this->outputTemplateString
                )
            )
        );

        return $this->reportSuccess($console, $width, $length);
    }

    /**
     * Create content to write to the output file
     *
     * Uses the passed data and template to generate content.
     */
    private function createContentFromData(array $data, string $template) : string
    {
        $escaper = new Escaper();
        $strings = array_map(function ($link) use ($template, $escaper) {
            return sprintf(
                $template,
                $link['link'],
                $escaper->escapeHtml($link['title'])
            );
        }, $data['links']);
        return implode("\n", $strings);
    }

    /**
     * Report an error
     *
     * @param Console $console
     * @param int $width
     * @param int $length
     * @param string|Throwable $e
     * @return int
     */
    private function reportError(Console $console, int $width, int $length, $e) : int
    {
        if (($length + 9) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 9;
        $console->writeLine(str_repeat('.', $spaces) . '[ ERROR ]', Color::RED);

        if (is_string($e)) {
            $console->writeLine($e);
        }

        if ($e instanceof Throwable) {
            $console->writeLine($e->getTraceAsString());
        }

        return 1;
    }

    /**
     * Report success
     */
    private function reportSuccess(Console $console, int $width, int $length) : int
    {
        if (($length + 8) > $width) {
            $console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 8;
        $console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
        return 0;
    }
}
