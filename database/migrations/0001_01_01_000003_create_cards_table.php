<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('card_name', 100);
            $table->string('full_name', 100);
            $table->string('job_title', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->string('qr_url')->nullable();
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
        Schema::dropIfExists('cards');
    }
}
