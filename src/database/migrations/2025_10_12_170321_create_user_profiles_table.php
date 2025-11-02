<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('path')->nullable();
            $table->string('postal_code', 8)->nullable();
            $table->string('address')->nullable();
            $table->string('building')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('user_profiles'); }
};

