<?php
namespace GithubFeed;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\RendererInterface as Renderer;

class FetchController extends AbstractActionController
{
    protected $console;
    protected $feedFile;
    protected $reader;
    protected $renderer;

    public function setConsole(Console $console)
    {
        $this->console = $console;
    }

    public function setFeedFile($path)
    {
        $this->feedFile = $path;
    }

    public function setReader(AtomReader $reader)
    {
        $this->reader = $reader;
    }

    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function feedAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw RuntimeException(sprintf(
                '%s can only be run via the Console',
                __METHOD__
            ));
        }

        $message = 'Retrieving Github activity links';
        $length  = strlen($message);
        $width   = $this->console->getWidth();

        $this->console->write($message, Color::BLUE);

        try {
            $data = $this->reader->read();
        } catch (\Exception $e) {
            $this->reportError($width, $length, $e);
            return;
        }

        $model = new ViewModel(array(
            'github' => $data,
        ));
        $model->setTemplate('github-feed/links');

        $content  = $this->renderer->render($model);
        file_put_contents($this->feedFile, $content);
        // file_put_contents('data/github-feed-links.phtml', $content);
        $this->reportSuccess($width, $length);
    }

    protected function reportError($width, $length, $e)
    {
        if (($length + 9) > $width) {
            $this->console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 9;
        $this->console->writeLine(str_repeat('.', $spaces) . '[ ERROR ]', Color::RED);
        $this->console->writeLine($e->getTraceAsString());
    }

    protected function reportSuccess($width, $length)
    {
        if (($length + 8) > $width) {
            $this->console->writeLine('');
            $length = 0;
        }
        $spaces = $width - $length - 8;
        $this->console->writeLine(str_repeat('.', $spaces) . '[ DONE ]', Color::GREEN);
    }
}
