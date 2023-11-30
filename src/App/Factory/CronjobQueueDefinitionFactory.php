<?php

declare(strict_types=1);

namespace Mwop\App\Factory;

use ZendHQ\JobQueue\JobOptions;
use ZendHQ\JobQueue\QueueDefinition;

class CronjobQueueDefinitionFactory
{
    public function __invoke(): QueueDefinition
    {
        return new QueueDefinition(
            QueueDefinition::PRIORITY_NORMAL,
            new JobOptions(
                JobOptions::PRIORITY_NORMAL,
                60, // timeout
                3,  // allowed retries
                30,  // retry wait time
                JobOptions::PERSIST_OUTPUT_ERROR,
                false, // validate SSL
            )
        );
    }
}
