<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedback extends Migration
{
    
    public function up()
    {

        Schema::create('feedback', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('title', 100)->nullable()->comment('');
            $table->string('content', 2000)->nullable()->comment('');
            $table->string('contact')->nullable()->comment('');

        });

    }

    
    public function down()
    {

    }
}
