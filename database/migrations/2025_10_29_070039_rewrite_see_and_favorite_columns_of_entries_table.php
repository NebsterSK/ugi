<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable()->after('is_seen');
            $table->timestamp('favorited_at')->nullable()->after('is_favorite');
        });

        DB::table('entries')->where('is_seen', 1)->update([
            'seen_at' => '2025-11-03 00:00:00',
        ]);

        DB::table('entries')->where('is_favorite', 1)->update([
            'favorited_at' => '2025-11-03 00:00:00',
        ]);

        Schema::table('entries', function (Blueprint $table) {
            $table->dropColumn('is_seen');
            $table->dropColumn('is_favorite');
        });
    }
};
