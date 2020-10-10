<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiToken extends Migration
{
    
    public function up()
    {
        Schema::create('api_token', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('token', 64)->nullable()->comment('Token');
            $table->string('data', 500)->nullable()->comment('Data');
            $table->timestamp('expireTime')->nullable()->comment('过期时间');

            $table->unique(['token']);
            $table->index(['expireTime']);

        });
    }

    
    public function down()
    {
            }
}
