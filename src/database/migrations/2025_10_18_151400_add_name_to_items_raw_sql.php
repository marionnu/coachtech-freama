<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 既にnameがあれば何もしない
        if (! in_array('name', Schema::getColumnListing('items'))) {
            // item_name の直後に name を追加（NULL許可）
            DB::statement("ALTER TABLE items ADD COLUMN name VARCHAR(100) NULL AFTER item_name");
            // 既存の item_name を name にコピー
            DB::statement("UPDATE items SET name = item_name WHERE item_name IS NOT NULL AND (name IS NULL OR name = '')");
        }
    }

    public function down(): void
    {
        if (in_array('name', Schema::getColumnListing('items'))) {
            DB::statement("ALTER TABLE items DROP COLUMN name");
        }
    }
};
