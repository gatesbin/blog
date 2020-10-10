<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPayOrderAddParam extends Migration
{
    
    public function up()
    {
        Schema::table('pay_order', function (Blueprint $table) {
            $table->string('param', 400)->nullable()->comment('');
        });
    }

    
    public function down()
    {

    }
}
