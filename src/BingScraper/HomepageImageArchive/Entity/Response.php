<?php

namespace BingScraper\HomepageImageArchive\Entity;

use Exception;

class Response
{
    private $resourceUrl;
    private $rawData;
    private $images;

    public function __construct($resourceUrl, $result)
    {
        $this->resourceUrl = $resourceUrl;
        $this->rawData = json_decode($result, true);

        $this->parseImages();
    }

    private function parseImages()
    {
        $this->images = [];
        $data = $this->rawData;

        if (!is_array($data)) {
            throw new Exception('Expected it to be array.');
        }
        if (!array_key_exists('images', $data)) {
            throw new Exception('Expected images key to exist.');
        }
        if (!is_array($data['images'])) {
            throw new Exception('Expected images to be array.');   
        }

        foreach ($data['images'] as $imageData) {
            $this->images[] = new Image($imageData);
        }
    }

    public function imageCount()
    {
        return count($this->images);
    }

    public function getImages()
    {
        return $this->images;
    }
}
