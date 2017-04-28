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
             ->setDescription('Get and download latest image from Bing Homepage Image Arcive');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $client = new Client();
        $result = $client->get();
        $imageData = $result['images'][0];

        $lastSaved = $this->getLastSaved();

        if ($lastSaved && $lastSaved->hash === $imageData['hsh']) {
            $this->out->writeln('Newest already saved.');
            return 0;
        }

        $this->out->writeln('Got new image. Saving it.');
        
        $image = new Image();
        $image->start_time = DateTime::createFromFormat('Ymd', $imageData['startdate']);
        $image->end_time = DateTime::createFromFormat('Ymd', $imageData['enddate']);
        $image->url = $imageData['url'];
        $image->copyright = $imageData['copyright'];
        $image->hash = $imageData['hsh'];
        $image->save();

        $this->out->writeln('Downloading image.');

        $download = new Download();
        $download->get($image);

        $this->out->writeln('New image saved to ' . $image->filepath);
    }

    private function getLastSaved()
    {
        return Image::orderBy('end_time', 'desc')->first();
    }
}
