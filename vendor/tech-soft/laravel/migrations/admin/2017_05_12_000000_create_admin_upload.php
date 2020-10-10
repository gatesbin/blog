<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUpload extends Migration
{
    
    public function up()
    {
        Schema::create('admin_upload', function (Blueprint $table) {

            $table->increments('id');
            $table->timestamps();

            $table->string('category', 10)->nullable()->comment('大类');
            $table->integer('dataId')->nullable()->comment('文件ID');

            $table->integer('adminUploadCategoryId')->nullable()->comment('分类ID');

            $table->index(['adminUploadCategoryId']);

        });
    }

    
    public function down()
    {

    }
}
