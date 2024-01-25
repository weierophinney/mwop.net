<?php

declare(strict_types=1);

namespace Mwop\ZendHQ\Console;

use Mwop\App\Factory\CronjobQueueDefinitionFactory;
use Mwop\Comics\ComicsEvent;
use Mwop\Mastodon\PostEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZendHQ\JobQueue\HTTPJob;
use ZendHQ\JobQueue\JobQueue;
use ZendHQ\JobQueue\Queue;
use ZendHQ\JobQueue\RecurringSchedule;

use function count;
use function json_encode;

class SetupRecurringJobs extends Command
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
        $this->registerComicsCronjob($output);
        $this->registerMastodonCronjob($output);

        return 0;
    }

    private function registerComicsCronjob(OutputInterface $output): void
    {
        $output->writeln('<info>Registering comics cronjob</info>');
        $queue = $this->getQueue('comics');
        $jobs  = $queue->getJobsByName('comics');
        if (count($jobs) !== 0) {
            return;
        }

        $job = new HTTPJob($this->workerUrl, HTTPJob::HTTP_METHOD_POST);
        $job->setName('comics');
        $job->addHeader('Content-Type', 'application/mwop-net-jq+json');
        $job->setRawBody(json_encode([
            'type' => ComicsEvent::class,
            'data' => [],
        ]));
        $queue->scheduleJob($job, new RecurringSchedule('0 0 */3 * * *'));
    }

    private function registerMastodonCronjob(OutputInterface $output): void
    {
        $output->writeln('<info>Registering Mastodon cronjob</info>');
        $queue = $this->getQueue('mastodon');
        $jobs  = $queue->getJobsByName('mastodon');
        if (count($jobs) !== 0) {
            return;
        }

        $job = new HTTPJob($this->workerUrl, HTTPJob::HTTP_METHOD_POST);
        $job->setName('mastodon');
        $job->addHeader('Content-Type', 'application/mwop-net-jq+json');
        $job->setRawBody(json_encode([
            'type' => PostEvent::class,
            'data' => [],
        ]));
        $queue->scheduleJob($job, new RecurringSchedule('0 */15 * * * *'));
    }

    private function getQueue(string $name): Queue
    {
        return $this->jq->hasQueue($name)
            ? $this->jq->getQueue($name)
            : $this->jq->addQueue($name, (new CronjobQueueDefinitionFactory())());
    }
}
