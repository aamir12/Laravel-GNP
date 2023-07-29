<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliverablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliverables', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->boolean('is_shipped',1)->default(0)->comment('1 Shipped 0 Not Shipped');
            $table->string('shipping_name')->nullable();
            $table->string('shipping_number')->nullable();
            $table->string('shipping_email')->nullable();
            $table->string('shipping_addressline1')->nullable();
            $table->string('shipping_addressline2')->nullable();
            $table->string('shipping_addressline3')->nullable();
            $table->string('shipping_postcode')->nullable();
            $table->string('shipping_county')->nullable();
            $table->string('shipping_country')->nullable();
            $table->longText('shipping_comment')->nullable();
            $table->string('tracking_ref')->nullable();
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
        Schema::dropIfExists('deliverables');
    }
}
