<?php

namespace BingScraper\HomepageImageArchive\Entity;

use Exception;
use Carbon\Carbon as DateTime;

class Image
{
    public $startdate;
    public $enddate;
    public $url;
    public $copyright;
    public $hsh;
    
    public function __construct($data)
    {
        $dateFormat = 'Ymd';

        if (!is_array($data)) {
            throw new Exception('Expected image data to be array.');
        }

        if (!array_key_exists('startdate', $data)) {
            throw new Exception('Expected image to have startdate.');
        }
        $startdate = DateTime::createFromFormat($dateFormat, $data['startdate']);
        if (!$startdate) {
            throw new Exception('Expected image valid startdate in format ' . $dateFormat);   
        } else {
            $this->startdate = $startdate;
        }

        if (!array_key_exists('enddate', $data)) {
            throw new Exception('Expected image to have enddate.');
        }
        $enddate = DateTime::createFromFormat($dateFormat, $data['enddate']);
        if (!$enddate) {
            throw new Exception('Expected image valid enddate in format ' . $dateFormat);   
        } else {
            $this->enddate = $enddate;
        }

        if (!array_key_exists('url', $data)) {
            throw new Exception('Expected image to have url.');
        } else {
            $this->url = $data['url'];
        }

        if (!array_key_exists('copyright', $data)) {
            throw new Exception('Expected image to have copyright.');
        } else {
            $this->copyright = $data['copyright'];
        }

        if (!array_key_exists('hsh', $data)) {
            throw new Exception('Expected image to have hsh.');
        } else {
            $this->hsh = $data['hsh'];
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
