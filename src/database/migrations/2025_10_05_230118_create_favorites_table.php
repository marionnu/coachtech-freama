<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'item_id']);
            $table->index('user_id');
            $table->index('item_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}
