<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id')->unsigned()->comment('供应商');
            $table->integer('batch')->default(0)->comment('当前批次');
            $table->string('no')->unique()->comment('订单编号');
            $table->text('product')->comment('产品');
            $table->text('product_batch')->nullable()->comment('入库批次记录');
            $table->string('signing_at')->nullable()->comment('签订日');
            $table->string('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('orders');
    }
}
