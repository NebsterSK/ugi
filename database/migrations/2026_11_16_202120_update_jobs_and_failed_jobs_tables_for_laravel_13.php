<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedSmallInteger('attempts')->change();
        });

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->string('connection')->change();
            $table->string('queue')->change();

            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropIndex(['connection', 'queue', 'failed_at']);

            $table->text('connection')->change();
            $table->text('queue')->change();
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->change();
        });
    }
};
