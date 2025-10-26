<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->string('internal_id')->unique();
            $table->string('url')->nullable(false)->unique();
            $table->string('title')->nullable(false);
            $table->boolean('is_seen')->nullable(false)->default(false);
            $table->boolean('is_favorite')->nullable(false)->default(false);
            $table->boolean('is_ignored')->nullable(false)->default(false);
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
