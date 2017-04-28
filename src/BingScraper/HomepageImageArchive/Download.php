<?php

namespace BingScraper\HomepageImageArchive;

use BingScraper\Database\Model\Image;
use Carbon\Carbon;

class Download
{
    public function get(Image $image)
    {
        $dir = $this->getImageDir($image);
        $file = $this->getFilename($image);
        $toFile = $dir . '/' . $file;

        $this->download($image->getImageUrl(), $toFile);

        $image->filepath = $toFile;
        $image->save();
    }

    private function getImageDir(Image $image)
    {
        $start = $image->start_time;
        
        $dir = IMAGE_DIR . '/' . $start->format('Y') . '/' . $start->format('m');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }

    private function getFilename(Image $image)
    {
        // Url is path.
        return basename($image->url);
    }

    private function download($fromUrl, $toFile)
    {
        $file = fopen($toFile, 'wb');

        $curl = curl_init($fromUrl);
        curl_setopt($curl, CURLOPT_FILE, $file);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_exec($curl);
        curl_close($curl);

        fclose($file);
    }
}
