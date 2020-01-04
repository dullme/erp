<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_id')->unsigned()->comment('订单ID');
            $table->integer('order_batch')->default(1)->comment('入库批次');
            $table->integer('product_id')->unsigned()->comment('所属产品');
            $table->integer('status')->default(1)->comment('所属仓库:1中国仓库；2海运中；3美国备用仓库；4美国线上');
            $table->integer('quantity')->comment('数量');
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
        Schema::dropIfExists('warehouses');
    }
}
