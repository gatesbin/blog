<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLog extends Migration
{
    
    public function up()
    {

        Schema::create('admin_log', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->integer('adminUserId')->nullable()->comment('用户ID');
            
            $table->tinyInteger('type')->nullable()->comment('类型');
            $table->string('summary', 400)->nullable()->comment('摘要');

        });

        Schema::create('admin_log_data', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->text('content')->nullable()->comment('内容');

        });

    }

    
    public function down()
    {
    }
}
