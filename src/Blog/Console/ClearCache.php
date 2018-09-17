<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearCache extends Command
{
    private $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
        parent::__construct('blog:clear-cache');
    }

    protected function configure()
    {
        $this->setDescription('Clear the blog post cache');
        $this->setHelp('Clear the blog post cache');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Removing cached blog entries');

        if (! $this->cache->clear()) {
            $io->error('Cache pool indicated unsuccessful clear operation');
            return 1;
        }

        $io->success('SUCCESS');
        return 0;
    }
}
