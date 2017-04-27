<?php

namespace BingScraper\Database\Model;

class Image extends Model
{
	public function getImageUrl()
	{
		if ($this->url) {
			return 'http://www.bing.com' . $this->url;
		} else {
			return null;
		}
	}
}
