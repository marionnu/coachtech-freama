<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE items DROP COLUMN item_name");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE items ADD COLUMN item_name VARCHAR(255) NOT NULL AFTER user_id");
    }
};

