<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_profits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('asin')->comment('ASKU');
            $table->integer('quantity')->comment('数量');
            $table->decimal('ddp', 10, 2)->comment('价格');
            $table->text('products')->nullable();
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
        Schema::dropIfExists('sales_profits');
    }
}
