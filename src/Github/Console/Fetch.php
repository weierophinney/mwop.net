<?php
namespace Mwop\Github\Console;

use Exception;
use Mwop\Github;
use Zend\Console\ColorInterface as Color;
use Zend\Escaper\Escaper;

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

    /**
     * @param Github\AtomReader $reader
     * @param mixed $outputTemplateString
     */
    public function __construct(Github\AtomReader $reader = null, $outputTemplateString = null)
    {
        $this->reader = $reader;
        if (is_string($outputTemplateString) && ! empty($outputTemplateString)) {
            $this->outputTemplateString = $outputTemplateString;
        }
    }

    /**
     * Handle the incoming console request
     *
     * @param  \ZF\Console\Route $route
     * @param  \Zend\Console\Adapter\AdapterInterface $console
     * @return int
     */
    public function __invoke($route, $console)
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
        } catch (Exception $e) {
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
     *
     * @param array $data
     * @param string $template
     * @return string
     */
    private function createContentFromData($data, $template)
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
     * @param \Zend\Console\Adapter\AdapterInterface $console
     * @param int $width
     * @param int $length
     * @param string|Exception $e
     * @return int
     */
    private function reportError($console, $width, $length, $e)
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

        if ($e instanceof Exception) {
            $console->writeLine($e->getTraceAsString());
        }

        return 1;
    }

    /**
     * Report success
     *
     * @param \Zend\Console\Adapter\AdapterInterface $console
     * @param int $width
     * @param int $length
     * @return int
     */
    private function reportSuccess($console, $width, $length)
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
