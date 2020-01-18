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
            $table->integer('status')->default(0)->comment('状态 0:可入库；1：订单已完成');
            $table->integer('supplier_id')->unsigned()->comment('生产商');
            $table->integer('customer_id')->unsigned()->comment('进口商');
            $table->integer('batch')->default(0)->comment('当前批次');
            $table->string('no')->unique()->comment('订单编号');
            $table->string('signing_at')->nullable()->comment('签订日');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamp('finished_at')->nullable()->comment('订单完成时间');
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
