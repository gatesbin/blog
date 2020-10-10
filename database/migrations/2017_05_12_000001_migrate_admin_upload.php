<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateAdminUpload extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $datas = \TechOnline\Laravel\Dao\ModelUtil::all('data');
        foreach ($datas as $data) {
            \TechOnline\Laravel\Dao\ModelUtil::insert('admin_upload', [
                'category' => $data['category'],
                'dataId' => $data['id'],
                'adminUploadCategoryId' => 0,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
