<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('composes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique()->comment('名称');
            $table->string('asin')->unique();
            $table->string('hq')->nullable()->nullable();
            $table->string('image')->nullable()->comment('图片');
            $table->longText('content')->nullable()->comment('详情');
            $table->integer('count')->default(1)->comment('所需数量');
            $table->integer('order')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('composes');
    }
}
