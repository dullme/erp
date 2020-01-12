<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id')->unsigned()->comment('订单ID');
            $table->integer('product_id')->unsigned()->comment('产品ID');
            $table->integer('quantity')->comment('数量');
            $table->decimal('price', 10, 2)->comment('价格');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamp('inspection_at')->nullable()->comment('验货时间');
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
        Schema::dropIfExists('order_products');
    }
}
