<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_batches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status')->default(0)->comment('状态:0待审核：1审核通过');
            $table->integer('order_id')->unsigned()->comment('订单ID');
            $table->integer('batch')->comment('批次');
            $table->text('product_batch')->nullable()->comment('入库批次记录');
            $table->timestamp('entry_at')->nullable()->comment('入库时间');
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
        Schema::dropIfExists('order_batches');
    }
}
