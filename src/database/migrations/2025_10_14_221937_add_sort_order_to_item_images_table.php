<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('item_images', function (Blueprint $table) {
            if (!Schema::hasColumn('item_images', 'sort_order')) {
                $table->unsignedTinyInteger('sort_order')
                      ->default(1)
                      ->after('path');
            }
        });

        DB::table('item_images')->whereNull('sort_order')->update(['sort_order' => 1]);
    }

    public function down(): void
    {
        Schema::table('item_images', function (Blueprint $table) {
            if (Schema::hasColumn('item_images', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
