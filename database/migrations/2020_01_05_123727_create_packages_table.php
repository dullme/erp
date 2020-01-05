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
            $table->integer('forwarding_company_id')->unsigned()->comment('货代');
            $table->string('lading_number')->unique()->comment('提单号');
            $table->string('container_number')->unique()->comment('集装箱号');
            $table->string('seal_number')->unique()->comment('铅封号');
            $table->text('product')->comment('产品');
            $table->string('packaged_at')->nullable()->comment('装箱日');
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
        Schema::dropIfExists('packages');
    }
}
