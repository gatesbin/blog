<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenant extends Migration
{
    
    public function up()
    {

        Schema::create('tenant', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('tenant', 100)->nullable()->comment('');

            $table->integer('connectionId')->nullable()->comment('');
            
            $table->tinyInteger('type')->nullable()->comment('');

            $table->unique('tenant');
            $table->index('connectionId');
        });

        Schema::create('tenant_connection', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('used')->nullable()->comment('');
            $table->integer('available')->nullable()->comment('');
            $table->integer('priority')->nullable()->comment('');

            $table->string('host', 100)->nullable()->comment('');
            $table->string('database', 50)->nullable()->comment('');
            $table->string('username', 50)->nullable()->comment('');
            $table->string('password', 50)->nullable()->comment('');

        });

    }

    
    public function down()
    {

    }
}
