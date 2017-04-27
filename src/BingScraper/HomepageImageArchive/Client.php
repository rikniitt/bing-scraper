<?php

namespace BingScraper\HomepageImageArchive;

class Client
{
    const URL = 'http://www.bing.com/HPImageArchive.aspx?format=js&idx=%d&n=%d&mkt=en-US';

    public function get($offset = 0, $limit = 1)
    {
        $url = sprintf(self::URL, $offset, $limit);
        return json_decode($this->request($url), true);
    }

    private function request($url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
