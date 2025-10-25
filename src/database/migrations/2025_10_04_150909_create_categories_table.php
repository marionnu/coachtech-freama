<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                              // id BIGINT 主キー
        $table->string('category_name', 100);      // カテゴリ名
        $table->timestamps();                      // created_at / updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
