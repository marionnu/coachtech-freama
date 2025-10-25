<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // 売却日時（未売却は NULL）
            $table->dateTime('sold_at')->nullable()->after('status');
            // よく検索するならインデックスを付けてもOK
            // $table->index('sold_at');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('sold_at');
            // $table->dropIndex(['sold_at']); // 付けた場合のみ
        });
    }
};
