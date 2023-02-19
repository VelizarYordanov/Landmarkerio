<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->comment('');
            $table->integer('id', true)->index('id_2');
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->string('place_id')->index('place_id');
            $table->integer('route_id')->index('route_id');
            $table->integer('time_spent');
            $table->date('date');

            $table->index(['id'], 'id');
            $table->index(['user_id'], 'user_id_2');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visits');
    }
};
