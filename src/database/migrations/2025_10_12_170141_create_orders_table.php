<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('price');            // 決済金額（円）
            $table->tinyInteger('payment_method');       // 1:コンビニ 2:カード（仕様書に合わせてtinyint）
            $table->enum('status', ['pending','paid','canceled'])->default('pending'); // 追加推奨
            $table->string('stripe_session_id')->nullable();       // 追加推奨（将来のStripe用）
            $table->string('stripe_payment_intent')->nullable();   // 追加推奨
            $table->timestamps();

            $table->index(['buyer_id','status']);
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};

