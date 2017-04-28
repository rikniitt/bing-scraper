<?php

namespace BingScraper\Console\Command\HPIA;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BingScraper\HomepageImageArchive\Client;
use BingScraper\HomepageImageArchive\Download;
use BingScraper\Database\Model\Image;
use Carbon\Carbon as DateTime;

class GetLatest extends Command
{
    protected function configure()
    {
        $this->setName('hpia:get-latest')
             ->setDescription('Get and download latest image from Bing Homepage Image Archive');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $container = $this->getContainer();

        $client = new Client($container['logger']);
        $result = $client->get();

        if (!$result || !$result->imageCount()) {
            $this->out->writeln('Failed to get latest image');
        }

        $entity = $result->getImages()[0];
        $lastSaved = Image::latest()->first();

        if ($lastSaved && $lastSaved->hash === $entity->hsh) {
            $this->out->writeln('Newest already saved.');
            return 0;
        }

        $this->out->writeln("Got new image $entity. Saving it");
        
        $image = Image::createFromImageEntity($entity);
        $image->save();

        $this->out->writeln('Downloading image.');

        $download = new Download();
        $download->get($image);

        $this->out->writeln('New image saved to ' . $image->filepath);
    }
}
