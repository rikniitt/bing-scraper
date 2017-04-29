<?php

use BingScraper\Database\Migration;
use Illuminate\Database\Schema\Builder;

class CreateImagesTable implements Migration
{
    public function up(Builder $schemaBuilder)
    {
        $schemaBuilder->create('images', function($table) {
            $table->increments('id');
            $table->dateTime('startTime');
            $table->dateTime('endTime');
            $table->string('url');
            $table->string('copyright');
            $table->string('hash');
            $table->timestamps();
        });
    }
}
