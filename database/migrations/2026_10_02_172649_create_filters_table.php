<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('property_type')->nullable(false);
            $table->json('locations')->nullable(false);
            $table->integer('price_from')->unsigned()->nullable();
            $table->integer('price_to')->unsigned()->nullable();
            $table->integer('area_from')->unsigned()->nullable();
            $table->boolean('is_active')->nullable(false)->default(true);
            $table->timestamps();
        });

        DB::table('filters')->insert([
            'name' => '4-room apartments',
            'property_type' => '4-izbove-byty',
            'locations' => json_encode([100012514, 100012524, 100012513, 100012511]),
            'price_from' => 240000,
            'price_to' => 280000,
            'area_from' => 75,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('filters');
    }
};
