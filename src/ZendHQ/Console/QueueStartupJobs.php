<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Console;

use DateTimeImmutable;
use Mwop\Mastodon\PostEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZendHQ\JobQueue\HTTPJob;
use ZendHQ\JobQueue\JobQueue;
use ZendHQ\JobQueue\ScheduledTime;

use function json_encode;

class QueueStartupJobs extends Command
{
    public function __construct(
        private JobQueue $jq,
        /**
         * @psalm-var non-empty-string
         * @psalm-param non-empty-string $workerUrl
         */
        private string $workerUrl,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->registerMastodonJob($output);

        return 0;
    }

    private function registerMastodonJob(OutputInterface $output): void
    {
        $output->writeln('<info>Queuing initial fetch of data from Mastodon</info>');
        $queue = $this->jq->getDefaultQueue();
        $job   = new HTTPJob($this->workerUrl, HTTPJob::HTTP_METHOD_POST);
        $job->setName('mastodon');
        $job->addHeader('Content-Type', 'application/mwop-net-jq+json');
        $job->setRawBody(json_encode([
            'type' => PostEvent::class,
            'data' => [],
        ]));
        $queue->scheduleJob($job, new ScheduledTime(new DateTimeImmutable('+1 minute')));
    }
}
