<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfig extends Migration
{
    
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('key', 100)->nullable()->comment('键值');
            $table->string('value', 20000)->nullable()->comment('值');

            $table->unique('key');
        });
    }

    
    public function down()
    {

    }
}
