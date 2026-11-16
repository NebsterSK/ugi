<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sniff_runs', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable(false);
            $table->unsignedSmallInteger('filters_count')->nullable(false)->default(0);
            $table->unsignedInteger('saved_count')->nullable(false)->default(0);
            $table->unsignedInteger('skipped_count')->nullable(false)->default(0);
            $table->unsignedInteger('failed_count')->nullable(false)->default(0);
            $table->unsignedInteger('new_entries_count')->nullable(false)->default(0);
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable(false)->index();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sniff_runs');
    }
};
