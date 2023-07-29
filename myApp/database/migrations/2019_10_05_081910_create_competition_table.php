<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description');
            $table->string('image');
            $table->enum('type', ['Fixed', 'Rolling'])->default('Fixed');   
            $table->integer('score');
            $table->enum('period',['daily','monthly','weekly']);    
            $table->timestamp('start_date')->nullable();    
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_lottery',1)->default(1);   
            $table->integer('space_count')->default(0)->comment('0 Infinite space');
            $table->decimal('entry_fee',10,2); 
            $table->longText('terms_and_condition');
            $table->integer('group');
            $table->boolean('auto_enter_user',1)->default(0)->comment('1 Auto assign all group users');
            $table->boolean('is_deleted',20)->default(0)->comment('1 Delete 0 NotDeleted');
            $table->enum('image_type',['local','S3'])->nullable();
            $table->enum('status',['draft','live','archived'])->default('draft');
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
        Schema::dropIfExists('competitions');
    }
}
