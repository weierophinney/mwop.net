<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class PostLatestToMastodon extends Command
{
    private const HELP = <<<'END'
        Finds the most recent blog post, and posts to Mastodon with details about it.
        END;

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private string $tokenHeader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Post the most recent blog post to Mastodon');
        $this->setHelp(self::HELP);
        $this->addArgument('apikey', InputArgument::REQUIRED, 'mwop.net blog API key to use when making the request');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Posting to Mastodon with details of latest blog post</info>');
        $apiKey = $input->getArgument('apikey');

        $client   = HttpClientDiscovery::find();
        $request  = $this->requestFactory
            ->createRequest('POST', 'https://mwop.net/blog/api/mastodon/latest')
            ->withAddedHeader($this->tokenHeader, $apiKey);
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
