<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'name')) {
                $table->string('name', 100)->after('category_name');
            }
        });
        DB::statement("UPDATE categories SET name = category_name WHERE name IS NULL OR name = ''");
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
