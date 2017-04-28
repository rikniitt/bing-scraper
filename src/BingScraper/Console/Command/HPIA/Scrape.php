<?php

namespace BingScraper\Console\Command\HPIA;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use BingScraper\HomepageImageArchive\Client;
use BingScraper\Database\Model\Image;

class Scrape extends Command
{
    private $shouldStop;

    protected function configure()
    {
        $this->setName('hpia:scrape')
             ->setDescription('Scrape images from Bing Homepage Image Archive')
             ->addArgument('offset', InputArgument::OPTIONAL, 'Where to start from?', 0)
             ->addArgument('limit', InputArgument::OPTIONAL, 'How many to get per request?', 8);
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $offset = (int) $this->in->getArgument('offset');
        $limit = (int) max(1, min($this->in->getArgument('limit'), 8));

        $out->writeln("Starting to fetch images from offset $offset and $limit images per request.");

        // Setup traps
        $this->shouldStop = false;
        pcntl_signal(SIGTERM, [$this, 'shouldStop']);
        pcntl_signal(SIGINT, [$this, 'shouldStop']);

        while (true) {
            pcntl_signal_dispatch();
            if ($this->shouldStop) {
                $out->writeln('Catched signal. Exiting.');
                break; 
            }
            
            $offset = $this->process($offset, $limit);

            // Pick some random sleep time between 1-10 seconds.
            $secs = rand(1, 10);
            $out->writeln("Going to sleep $secs seconds.");
            sleep($secs);
        }

        $out->writeln("Finished to scrape. Continue from $offset");
    }

    public function shouldStop()
    {
        $this->shouldStop = true;
    }

    private function process($offset, $limit) 
    {
        $container = $this->getContainer();

        $this->out->writeln("Next request from $offset.");

        $client = new Client($container['logger']);
        $result = $client->get($offset, $limit);

        if (!$result || !$result->imageCount()) {
            $this->out->writeln('Failed to fetch images.');
            return $offset;
        }

        foreach ($result->getImages() as $entity) {
            $hash = $entity->hsh;
            if (Image::where('hash', $hash)->first()) {
                $this->out->writeln("Image with hash $hash already saved. Skip.");
            } else {
                $this->out->writeln("Got new image $entity. Saving it.");
                $image = Image::createFromImageEntity($entity);
                $image->save();
            }
        }

        return $offset + $limit;
    }
}
