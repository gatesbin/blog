<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiApp extends Migration
{
    
    public function up()
    {

        Schema::create('api_app', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('name', 50)->nullable()->comment('应用名称');
            $table->string('appId', 32)->nullable()->comment('AppId');
            $table->string('appSecret', 32)->nullable()->comment('AppSecret');

            $table->tinyInteger('moduleXxxEnable')->nullable()->comment('功能');
            $table->date('moduleXxxExpire')->nullable()->comment('功能');

            $table->unique(['appId']);

        });

    }

    
    public function down()
    {

    }
}
