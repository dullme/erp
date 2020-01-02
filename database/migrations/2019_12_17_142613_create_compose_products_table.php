<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComposeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compose_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('compose_id')->unsigned()->comment('所属组合');
            $table->integer('product_id')->unsigned()->comment('所属产品');
            $table->integer('quantity')->comment('数量');
            $table->integer('hq')->comment('hq');
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
        Schema::dropIfExists('compose_products');
    }
}
