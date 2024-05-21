<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->longText('description');
            $table->integer('status_id'); 
            $table->integer('assignment_id');
            $table->boolean('is_question')->default(false);
            $table->date('start_date'); 
            $table->date('end_date');    
            $table->dateTime('published_at')->nullable();   
            $table->integer('published_by')->nullable();   
            $table->integer('created_by')->nullable(); 
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
        Schema::dropIfExists('forms');
    }
}
