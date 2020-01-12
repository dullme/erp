<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status')->default(0)->comment('状态0:待审核；1待入库；2已入库');
            $table->integer('forwarding_company_id')->unsigned()->comment('货代');
            $table->integer('buyer_id')->unsigned()->comment('采购商');
            $table->integer('customer_id')->unsigned()->comment('客户');
            $table->string('lading_number')->comment('提单号');
            $table->string('agreement_no')->comment('合同号');
            $table->string('container_number')->nullable()->comment('集装箱号');
            $table->string('seal_number')->nullable()->comment('铅封号');
            $table->text('product')->comment('产品');
            $table->string('ship_port')->comment('发货港');
            $table->string('arrival_port')->comment('到货港');
            $table->text('remark')->nullable()->comment('备注');
            $table->boolean('report')->default(1)->comment('是否报告');
            $table->timestamp('packaged_at')->nullable()->comment('发货日');
            $table->timestamp('departure_at')->nullable()->comment('离港时间');
            $table->timestamp('arrival_at')->nullable()->comment('到港时间');
            $table->timestamp('entry_at')->nullable()->comment('预计入仓时间');
            $table->timestamp('checkin_at')->nullable()->comment('实际入仓时间');
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
        Schema::dropIfExists('packages');
    }
}
