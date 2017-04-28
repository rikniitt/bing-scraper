<?php

namespace BingScraper\Console\Command\HPIA;

use BingScraper\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BingScraper\HomepageImageArchive\Download as DownloadLib;
use BingScraper\Database\Model\Image;
use Symfony\Component\Console\Helper\ProgressBar;
use Carbon\Carbon as DateTime;

class Download extends Command
{
    private $shouldStop;

    protected function configure()
    {
        $this->setName('hpia:download')
             ->setDescription('Download images scraped from Bing Homepage Image Archive');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        parent::execute($in, $out);

        $container = $this->getContainer();
        $download = new DownloadLib();

        $out->writeln("Starting to download images.");

        // Setup traps
        $this->shouldStop = false;
        pcntl_signal(SIGTERM, [$this, 'shouldStop']);
        pcntl_signal(SIGINT, [$this, 'shouldStop']);

        $progress = new ProgressBar($out, Image::notDownloaded()->count());
        $progress->start();

        while (true) {
            pcntl_signal_dispatch();
            if ($this->shouldStop) {
                $out->writeln('Catched signal. Exiting.');
                break; 
            }

            $next = Image::notDownloaded()->first();
            if (!$next) {
                $progress->finish();
                $out->writeln('');
                $out->writeln("Finished to download.");
                break;
            }

            $this->ensureDateTimeFields($next);

            $download->get($next);
            $progress->advance();

            // Pick some random sleep time between 1-10 seconds.
            $secs = rand(1, 10);
            sleep($secs);
        }
    }

    public function shouldStop()
    {
        $this->shouldStop = true;
    }

    private function ensureDateTimeFields(Image $image)
    {
        // start_time and end_time should already be DateTime\Carbon instances?
        if (is_string($image->start_time)) {
            $image->start_time = new DateTime($image->start_time);
        }
        if (is_string($image->end_time)) {
            $image->end_time = new DateTime($image->end_time);
        }
    }
}
