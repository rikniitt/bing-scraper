<?php

namespace BingScraper\HomepageImageArchive;

use Monolog\Logger;
use Exception;

class Client
{

    const URL = 'http://www.bing.com/HPImageArchive.aspx?format=js&idx=%d&n=%d&mkt=en-US';
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function get($offset = 0, $limit = 1)
    {
        $url = sprintf(self::URL, $offset, $limit);
        $result = $this->request($url);

        try {
            return new Entity\Response($url, $result);
        } catch (Exception $e) {
            $this->logger->error('BingScraper\HomepageImageArchive\Client - Error processing result: ' . $e->getMessage());
            return false;
        }
    }

    private function request($url)
    {
        $curl = curl_init();

        $this->logger->info('BingScraper\HomepageImageArchive\Client - Doing HTTP GET to ' . $url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 

        $result = curl_exec($curl);
        curl_close($curl);

        $this->logger->info('BingScraper\HomepageImageArchive\Client - Got result: ' . $result);

        return $result;
    }
}
