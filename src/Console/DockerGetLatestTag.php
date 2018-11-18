<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use DateTimeImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DockerGetLatestTag extends Command
{
    private const HELP = <<< 'EOH'
Retrieves the latest tag for the given package by looping through the
list of tags, and looking for the one with the most recent date/time
tag string.

EOH;

    private const TEMPLATE_URL = 'https://registry.hub.docker.com/v2/repositories/%s/tags/';

    public function __construct(string $name = 'docker:get-latest-tag')
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Fetch the most recent tag for a package.');
        $this->setHelp(self::HELP);

        $this->addArgument(
            'package',
            InputArgument::REQUIRED,
            'Package for which to fetch version'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $package = $input->getArgument('package');
        $url     = sprintf(self::TEMPLATE_URL, $package);

        $json = file_get_contents($url);
        $data = json_decode($json);

        if (! isset($data->results) || 0 === count($data->results)) {
            $output->writeln(sprintf('<error>No tags found for %s</error>', $package));
            return 1;
        }

        $mostRecent  = null;
        $lastUpdated = null;

        foreach ($data->results as $result) {
            if (null === $mostRecent) {
                $mostRecent = $result->name;
                $lastUpdated = new DateTimeImmutable($result->last_updated);
                continue;
            }

            $test = new DateTimeImmutable($result->last_updated);
            if ($test > $lastUpdated) {
                $mostRecent = $result->name;
                $lastUpdated = $test;
                continue;
            }
        }

        $output->write($mostRecent);

        return 0;
    }
}
