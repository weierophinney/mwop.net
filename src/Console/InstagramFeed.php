<?php

/**
 * @copyright Copyright (c) Matthew Weier O'Phinney
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 */

declare(strict_types=1);

namespace Mwop\Console;

use ArrayAccess;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_map;
use function file_put_contents;
use function get_class;
use function getcwd;
use function implode;
use function realpath;
use function rtrim;
use function sprintf;

class InstagramFeed extends Command
{
    /** @var string */
    private const CACHE_FILE = '%s/data/instagram.feed.php';

    private string $configFormat = <<<EOC
        <?php
        return [
        %s
        ];
        
        EOC;

    private string $configItemFormat = <<<EOC
        [
            'image_url' => '%s',
            'post_url'  => '%s',
        ],
        
        EOC;

    public function __construct(private InstagramClient $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('instagram-feeds');
        $this->setDescription('Fetch instagram feed data.');
        $this->setHelp('Fetch photo URLs for homepage instagram stream.');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Application root path',
            realpath(getcwd())
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Aggregating instagram feed URLs for home page');

        try {
            $feed = $this->client->fetchFeed();
        } catch (Exception $e) {
            $io->error(sprintf(
                'Error fetching Instagram feed: (%s) %s',
                get_class($e),
                $e->getMessage()
            ));
            return 1;
        }

        $filename = $this->generateFilename($input->getOption('path'));
        file_put_contents($filename, $this->generateContent($feed));

        $io->success(sprintf('Aggregated instagram feed URLs to %s.', $filename));

        return 0;
    }

    private function generateFilename(string $path): string
    {
        return sprintf(self::CACHE_FILE, $path);
    }

    /**
     * @param array $feed array<string, string>
     */
    private function generateContent(array $feed): string
    {
        $entries = implode('', array_map(
            // phpcs:ignore
            function (array|ArrayAccess $entry): string {
                return sprintf($this->configItemFormat, $entry['image_url'], $entry['post_url']);
            },
            $feed
        ));

        return sprintf($this->configFormat, rtrim($entries, "\n"));
    }
}
