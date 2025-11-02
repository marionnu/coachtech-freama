<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('categories', 'category_name')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('category_name', 100)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'category_name')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('category_name', 100)->nullable(false)->change();
            });
        }
    }
};
