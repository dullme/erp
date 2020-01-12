<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique()->comment('名称');
            $table->string('english_name')->nullable()->comment('英文名称');
            $table->string('contact_person')->nullable()->comment('联系人');
            $table->string('position')->nullable()->comment('职位');
            $table->string('mobile')->nullable()->comment('手机号');
            $table->string('tel')->nullable()->comment('座机');
            $table->string('fax')->nullable()->comment('传真');
            $table->string('email')->nullable()->comment('传真');
            $table->string('website')->nullable()->comment('网站');
            $table->string('address')->nullable()->comment('地址');
            $table->string('supply')->nullable()->comment('境内货源地');
            $table->string('tax_id')->nullable()->comment('税号');
            $table->string('bank')->nullable()->comment('开户行');
            $table->string('bank_account')->nullable()->comment('银行账号');
            $table->string('register')->nullable()->comment('登记');
            $table->text('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('customers');
    }
}
