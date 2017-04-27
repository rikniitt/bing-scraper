<?php

use BingScraper\Database\Migration;
use Illuminate\Database\Schema\Builder;

class RenameImagesColumns implements Migration
{

    public function up(Builder $schemaBuilder)
    {
        // Rename columns to follow illuminate naming conventions.
        // Also cant do both renames on same closure.

        $schemaBuilder->table('images', function($table) {
            $table->renameColumn('startTime', 'start_time');
            
        });
        $schemaBuilder->table('images', function($table) {
            $table->renameColumn('endTime', 'end_time');
        });
    }

}
