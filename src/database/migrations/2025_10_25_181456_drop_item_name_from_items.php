<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // あるときだけ削除
        DB::statement("ALTER TABLE items DROP COLUMN item_name");
    }

    public function down(): void
    {
        // 必要なら復元（NOT NULL で良ければ）
        DB::statement("ALTER TABLE items ADD COLUMN item_name VARCHAR(255) NOT NULL AFTER user_id");
    }
};

