<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSentMessageDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sent_message_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sent_message_id');
            $table->string('title');
            $table->text('detail');
            $table->string('ip', 64);
            $table->timestamp('time');
            $table->timestamps();
            $table->foreign('sent_message_id')->references('id')->on('sent_messages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sent_message_details');
    }
}
