<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCommentsTable extends Migration
{
    
    public function up()
    {
        Schema::create('item_comments', function (Blueprint $table) {
            $table->id();

            // 外部キー（ユーザー、アイテム）
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();

            // 本文
            $table->text('body');

            $table->timestamps();

            // よく使う並び替え・検索向け
            $table->index(['item_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_comments');
    }
}
