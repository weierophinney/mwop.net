<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstagramFeed extends Command
{
    /** @var string */
    private const CACHE_FILE = '%s/data/instagram.feed.php';

    /** @var string */
    private $configFormat = <<< EOC
<?php
return [
%s
];

EOC;

    private $configItemFormat = <<< EOC
    [
        'image_url' => '%s',
        'post_url'  => '%s',
    ],

EOC;

    /** @var InstagramClient */
    private $client;

    public function __construct(InstagramClient $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    protected function configure() : void
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

    protected function execute(InputInterface $input, OutputInterface $output) : int
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

        file_put_contents(
            $this->generateFilename($input->getOption('path')),
            $this->generateContent($feed)
        );

        $io->success('Aggregated instagram feed URLs.');

        return 0;
    }

    private function generateFilename(string $path) : string
    {
        return sprintf(self::CACHE_FILE, $path);
    }

    /**
     * @param array<string, string> $feed
     */
    private function generateContent(array $feed) : string
    {
        $entries = implode('', array_map(
            function ($entry) {
                return sprintf($this->configItemFormat, $entry['image_url'], $entry['post_url']);
            },
            $feed
        ));

        return sprintf($this->configFormat, rtrim($entries, "\n"));
    }
}
