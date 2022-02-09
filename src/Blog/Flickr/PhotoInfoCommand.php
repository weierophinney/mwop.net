<?php

declare(strict_types=1);

namespace Mwop\Blog\Flickr;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotoInfoCommand extends Command
{
    private const HELP = <<<'END'
        Allows retrieving photo information from Flickr, including each of:
        
        - The URL to the full-sized image
        - The name of the photographer
        - A URL to the web page with the photo, for attribution

        END;

    public function __construct(
        private Photos $photos,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Retrieve information, including attribution, for a photo on Flickr');
        $this->setHelp(self::HELP);
        $this->addArgument('photo-id', InputArgument::REQUIRED, 'Photo ID');
        $this->addArgument('secret', InputArgument::REQUIRED, 'Secret associated with photo');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id     = $input->getArgument('photo-id');
        $secret = $input->getArgument('secret');

        $output->writeln('<info>Pulling photo information...</info>');

        $photo = $this->photos->fetchImage($id, $secret);

        $output->writeln($photo);

        return 0;
    }
}
