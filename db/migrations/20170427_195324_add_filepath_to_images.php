<?php

use BingScraper\Database\Migration;
use Illuminate\Database\Schema\Builder;

class AddFilepathToImages implements Migration
{
    public function up(Builder $schemaBuilder)
    {
        $schemaBuilder->table('images', function($table) {
            $table->string('filepath')->nullable();
        });
    }
}
