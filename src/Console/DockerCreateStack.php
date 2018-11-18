<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

declare(strict_types=1);

namespace Mwop\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DockerCreateStack extends Command
{
    private const HELP = <<< 'EOH'
Generates the docker-stack.yml file to use during deployment, using
the specified tags for the redis, php, and caddy containers.

For any of the containers, if the string "latest" is used, this
script will look up the latest tagged version of that container
and use that to generate the docker-stack.yml file.

EOH;

    private const REPOMAP = [
        'caddy' => 'mwopswoolecaddy',
        'php'   => 'mwopswoole',
        'redis' => 'mwopredis',
    ];

    public function __construct(string $name = 'docker:create-stack')
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setDescription('Build the docker-stack.yml file.');
        $this->setHelp(self::HELP);

        $this->addOption(
            'caddy',
            'c',
            InputOption::VALUE_REQUIRED,
            'mwopswoolecadddy tag to use; "latest" indicates most recent',
            'latest'
        );

        $this->addOption(
            'php',
            'p',
            InputOption::VALUE_REQUIRED,
            'mwopswoole tag to use; "latest" indicates most recent',
            'latest'
        );

        $this->addOption(
            'redis',
            'r',
            InputOption::VALUE_REQUIRED,
            'mwopredis tag to use; "latest" indicates most recent',
            'latest'
        );

        $this->addOption(
            'stackfile',
            's',
            InputOption::VALUE_REQUIRED,
            'Name of the docker stack file to generate',
            'docker-stack.yml'
        );

        $this->addOption(
            'templatefile',
            't',
            InputOption::VALUE_REQUIRED,
            'Name of the docker stack template file',
            'docker-stack.yml.dist'
        );

        $this->addOption(
            'hubuser',
            'u',
            InputOption::VALUE_REQUIRED,
            'Name of the docker hub user/namespace',
            'mwop'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $template = $input->getOption('templatefile');
        if (! file_exists($template)) {
            $output->writeln('<error>Template file "%s" does not exist!</error>');
            return 1;
        }

        $stackContents = file_get_contents($template);

        if (null === ($caddyVersion = $this->getPackageVersion($input, 'caddy'))) {
            $output->writeln('<error>Error determining caddy container version to use.</error>');
            return 1;
        }

        if (null === ($phpVersion = $this->getPackageVersion($input, 'php'))) {
            $output->writeln('<error>Error determining PHP container version to use.</error>');
            return 1;
        }

        if (null === ($redisVersion = $this->getPackageVersion($input, 'redis'))) {
            $output->writeln('<error>Error determining redis container version to use.</error>');
            return 1;
        }

        $substitutions = [
            '{mwopswoolecaddy}' => $caddyVersion,
            '{mwopswoole}'      => $phpVersion,
            '{mwopredis}'       => $redisVersion,
        ];

        $stackContents = str_replace(
            array_keys($substitutions),
            array_values($substitutions),
            $stackContents
        );

        $outputFile = $input->getOption('stackfile');
        file_put_contents($outputFile, $stackContents);

        $output->writeln(sprintf('<info>Stack file written to %s</info>', $outputFile));

        return 0;
    }

    private function getPackageVersion(InputInterface $input, string $package) : ?string
    {
        $version = $input->getOption($package);
        if ('latest' !== $version) {
            return $version;
        }

        /** @var Command $findLatestCommand */
        $findLatestCommand = $this->getApplication()->find('docker:get-latest-tag');

        $output = new BufferedOutput();

        $result = $findLatestCommand->run(new ArrayInput([
            'command' => 'docker:get-latest-tag',
            'package' => sprintf('%s/%s', $input->getOption('hubuser'), self::REPOMAP[$package]),
        ]), $output);

        return $output->fetch() ?: null;
    }
}
