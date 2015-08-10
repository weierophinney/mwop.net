<?php
namespace Mwop\Blog\Console;

use Mwop\Blog;
use Zend\Console\ColorInterface as Color;

class TagCloud
{
    private $mapper;

    public function __construct(Blog\MapperInterface $mapper)
    {
        $this->mapper   = $mapper;
    }

    public function __invoke($route, $console)
    {
        $message = 'Creating tag cloud';
        $length  = strlen($message);
        $width   = $console->getWidth();
        $console->write($message, Color::BLUE);

        if (! $route->matchedParam('output')) {
            return $this->reportError($console, $width, $length, 'Missing output file');
        }
        $output = $route->getMatchedParam('output');

        $cloud  = $this->mapper->fetchTagCloud();
        $markup = sprintf(
            "<h4>Tag Cloud</h4>\n<div class=\"cloud\">%s</div>",
            $cloud->render()
        );

        file_put_contents($output, $markup);
        return $this->reportSuccess($console, $width, $length);
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
        $spaces = ($spaces > 0) ? $spaces : 0;
        $console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
        return 0;
    }
}
