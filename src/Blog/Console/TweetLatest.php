<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TweetLatest extends Command
{
    private const HELP = <<<'END'
        Finds the most recent blog post, and sends a tweet with details about it.
        END;

    protected function configure(): void
    {
        $this->setDescription('Tweet the most recent blog post');
        $this->setHelp(self::HELP);
        $this->addArgument('apikey', InputArgument::REQUIRED, 'mwop.net blog API key to use when making the request');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Sending tweet detailing latest blog post</info>');
        $apiKey = $input->getArgument('apikey');

        $client = HttpClientDiscovery::find();
        $messageFactory = MessageFactoryDiscovery::find();
        $request = $messageFactory
            ->createRequest('POST', 'https://mwop.net/blog/api/tweet/latest')
            ->withAddedHeader('X-MWOP-NET-BLOG-API-KEY', $apiKey);
        $response = $client->sendRequest($request);

        if ($response->getStatusCode() !== 204) {
            $output->writeln('<error>FAILED</error>');
            $output->writeln(sprintf(
                '<error>Your request failed with status %d (%s)</error>',
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));

            $contents = $response->getBody()->__toString();
            if (! empty($contents)) {
                $output->writeln(sprintf('<error>%s</error>', $contents));
            }

            return 1;
        }

        $output->writeln('<info>Success!</info>');
        return 0;
    }
}
