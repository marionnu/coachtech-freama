<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->string('item_name', 100);
        $table->string('brand_name', 100)->nullable();
        $table->integer('price');
        $table->text('description');
        $table->tinyInteger('condition');
        $table->tinyInteger('status')->default(0);
        $table->timestamps();
    });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}
