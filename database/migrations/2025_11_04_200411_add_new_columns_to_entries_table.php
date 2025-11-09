<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->tinyInteger('rooms')->unsigned()->nullable(false)->after('is_ignored');
            $table->string('street')->nullable(false)->after('rooms');
            $table->string('district')->nullable(false)->after('street');
            $table->integer('area')->unsigned()->nullable(false)->after('district');
            $table->integer('price')->unsigned()->nullable(false)->after('area');
            $table->integer('price_per_sqm')->unsigned()->nullable(false)->after('price');
        });
    }

};
