<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Github\Console;

use Laminas\Escaper\Escaper;
use Mwop\Github\AtomReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function array_map;
use function file_put_contents;
use function implode;
use function sprintf;
use function strlen;

/**
 * Fetch github user activity links
 */
class Fetch extends Command
{
    /** @var string */
    private $outputTemplateString = '<li><a href="%s">%s</a></li>';

    /** @var AtomReader */
    private $reader;

    public function __construct(?AtomReader $reader = null, string $outputTemplateString = '')
    {
        $this->reader = $reader;
        if (! empty($outputTemplateString)) {
            $this->outputTemplateString = $outputTemplateString;
        }

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('github:fetch-activity');
        $this->setDescription('Fetch GitHub activity stream.');
        $this->setHelp('Fetch GitHub activity stream and generate links for the home page.');

        $this->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Output file to which to write links',
            'data/github-links.phtml'
        );

        $this->addOption(
            'template',
            't',
            InputOption::VALUE_REQUIRED,
            'Template string to use when generating link output',
            $this->outputTemplateString
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Retrieving GitHub activity');

        try {
            $data = $this->reader->read();
        } catch (Throwable $e) {
            $io->error('Failed to retrieve github activiity');
            $io->writeln($e->getMessage());
            $io->writeln($e->getTraceAsString());
            return 1;
        }

        file_put_contents(
            $input->getOption('output'),
            $this->createContentFromData(
                $data,
                $input->getOption('template')
            )
        );

        $io->success('Retrieved GitHub activity.');

        return 0;
    }

    /**
     * Create content to write to the output file
     *
     * Uses the passed data and template to generate content.
     */
    private function createContentFromData(array $data, string $template): string
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
}
