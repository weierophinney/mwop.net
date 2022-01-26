<?php

declare(strict_types=1);

namespace Mwop\Blog\Console;

use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function json_encode;
use function sprintf;

class TweetPost extends Command
{
    private const HELP = <<<'END'
        Sends a tweet with details about the requested blog post.
        END;

    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private string $tokenHeader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Tweet details for the requested blog post');
        $this->setHelp(self::HELP);
        $this->addArgument(
            'apikey',
            InputArgument::REQUIRED,
            'mwop.net blog API key to use when making the request'
        );
        $this->addArgument(
            'post',
            InputArgument::REQUIRED,
            'Identifier (slug, minus path and extension) of blog post to tweet'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $post = $input->getArgument('post');
        $output->writeln(sprintf('<info>Sending tweet for blog post %s</info>', $post));

        $apiKey  = $input->getArgument('apikey');
        $client  = HttpClientDiscovery::find();
        $request = $this->requestFactory
            ->createRequest('POST', 'https://mwop.net/blog/api/tweet/latest')
            ->withAddedHeader($this->tokenHeader, $apiKey);
        $request->getBody()->write(json_encode(['id' => $post]));

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
