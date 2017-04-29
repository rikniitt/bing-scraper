<?php

namespace BingScraper\Database;

use Illuminate\Database\Schema\Builder;

interface Migration
{
    public function up(Builder $schemaBuilder);
}
