<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // category_id を NULL 許可にする（外部キーが付いていたら事前に外してから実行）
        DB::statement('ALTER TABLE items MODIFY category_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        // 元に戻す（必要なら）
        DB::statement('ALTER TABLE items MODIFY category_id BIGINT UNSIGNED NOT NULL');
    }
};
