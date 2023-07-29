<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeagueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->unsignedBigInteger('owner_id');
            $table->enum('score_aggregation_period',['daily','monthly','weekly']);
            $table->enum('type', ['Public', 'Private']);  
            $table->unsignedBigInteger('group_id')->default(0);
            $table->boolean('isDeleted',1)->default(0)->comment('1 Delete 0 NotDeleted');
            $table->enum('image_type',['local','S3'])->nullable();
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
        Schema::dropIfExists('leagues');
    }
}
