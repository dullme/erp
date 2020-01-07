<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku')->unique();
            $table->integer('length')->comment('长');
            $table->integer('width')->comment('宽');
            $table->integer('height')->comment('高');
            $table->decimal('weight', 10, 2)->comment('重量');
            $table->integer('ddp')->comment('价格');
            $table->string('hq')->nullable()->comment('hq');
            $table->string('unit')->nullable()->comment('单位');
            $table->string('image')->nullable()->comment('图片');
            $table->string('description')->nullable()->comment('描述');
            $table->longText('content')->nullable()->comment('详情');
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
        Schema::dropIfExists('products');
    }
}
