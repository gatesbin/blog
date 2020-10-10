<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticle extends Migration
{
    
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('position', 50)->nullable()->comment('位置');

            $table->string('title', 200)->nullable()->comment('标题');
            $table->text('content')->nullable()->comment('内容');

        });
    }

    
    public function down()
    {

    }
}
