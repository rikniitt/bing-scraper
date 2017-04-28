<?php

namespace BingScraper\Database\Model;

use BingScraper\HomepageImageArchive\Entity\Image as ImageEntity;
use Illuminate\Database\Eloquent\Builder as Query;

class Image extends Model
{
    public function scopeLatest(Query $query)
    {
        return $query->orderBy('end_time', 'desc');
    }

    public static function createFromImageEntity(ImageEntity $entity)
    {
        $image = new static();

        $image->start_time = $entity->startdate;
        $image->end_time = $entity->enddate;
        $image->url = $entity->url;
        $image->copyright = $entity->copyright;
        $image->hash = $entity->hsh;

        return $image;
    }

	public function getImageUrl()
	{
		if ($this->url) {
			return 'http://www.bing.com' . $this->url;
		} else {
			return null;
		}
	}
}
