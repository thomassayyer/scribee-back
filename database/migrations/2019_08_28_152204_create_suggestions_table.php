<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suggestions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('suggestion');
            $table->integer('start');
            $table->integer('end');
            $table->boolean('accepted')->default(false);
            $table->unsignedBigInteger('text_id');
            $table->string('user_pseudo');
            $table->timestamps();
            $table->foreign('text_id')->references('id')->on('texts')->onDelete('cascade');
            $table->foreign('user_pseudo')->references('pseudo')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suggestions');
    }
}
