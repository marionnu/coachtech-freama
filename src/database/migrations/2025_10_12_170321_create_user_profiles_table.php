<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('path')->nullable();            // アイコン等
            $table->string('postal_code', 8)->nullable();  // 123-4567
            $table->string('address')->nullable();         // 住所
            $table->string('building')->nullable();        // 建物名
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('user_profiles'); }
};

