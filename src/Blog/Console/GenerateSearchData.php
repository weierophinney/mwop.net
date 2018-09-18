<?php
/**
 * @license http://opensource.org/licenses/BSD-2-Clause BSD-2-Clause
 * @copyright Copyright (c) Matthew Weier O'Phinney
 */

namespace Mwop\Blog\Console;

use Mni\FrontYAML\Parser;
use Mwop\Blog\MarkdownFileFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateSearchData extends Command
{
    protected function configure()
    {
        $this->setName('blog:generate-search-data');
        $this->setDescription('Generate site search data.');
        $this->setHelp('Generate site search data based on blog posts.');

        $this->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED,
            'Base path of the application; posts are expected at $path/data/blog/ '
            . 'and search terms will be written to $path/public/js/search_terms.json',
            realpath(getcwd())
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);
        $basePath = $input->getOption('path');
        $path     = realpath($basePath) . '/data/blog';

        $io->title('Generating search metadata');

        $documents = [];
        $parser = new Parser();
        foreach (new MarkdownFileFilter($path) as $fileInfo) {
            $document = $parser->parse(file_get_contents($fileInfo->getPathname()), false);
            $metadata = $document->getYAML();
            $content  = $document->getContent();

            $documents[] = [
                'id'      => sprintf('/blog/%s.html', $metadata['id']),
                'tags'    => implode(' ', $metadata['tags']),
                'title'   => $metadata['title'],
                'content' => $content,
            ];
        }

        file_put_contents(
            realpath($basePath) . '/public/js/search_terms.json',
            json_encode(['docs' => $documents], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        $io->success('Generated search metadata');

        return 0;
    }
}
