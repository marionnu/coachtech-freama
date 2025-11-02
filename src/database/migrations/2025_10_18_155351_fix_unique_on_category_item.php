<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('category_item')) return;
        $rows = DB::select("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'category_item'
              AND NON_UNIQUE   = 0
            GROUP BY INDEX_NAME
            HAVING SUM(CASE WHEN COLUMN_NAME='item_id' THEN 1 ELSE 0 END) = 1
               AND COUNT(*) = 1
        ");
        foreach ($rows as $r) {
            $name = $r->INDEX_NAME;
            DB::statement("ALTER TABLE category_item DROP INDEX `{$name}`");
        }

        $exists = DB::select("
            SELECT 1
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'category_item'
              AND INDEX_NAME   = 'category_item_item_category_unique'
            LIMIT 1
        ");
        if (empty($exists)) {
            DB::statement("
                ALTER TABLE category_item
                ADD UNIQUE KEY category_item_item_category_unique (item_id, category_id)
            ");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('category_item')) return;
        $exists = DB::select("
            SELECT 1
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'category_item'
              AND INDEX_NAME   = 'category_item_item_category_unique'
            LIMIT 1
        ");
        if (!empty($exists)) {
            DB::statement("
                ALTER TABLE category_item
                DROP INDEX category_item_item_category_unique
            ");
        }
    }
};
