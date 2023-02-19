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
        Schema::create('favourite_routes', function (Blueprint $table) {
            $table->comment('');
            $table->integer('id', true);
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->integer('route_id')->index('route_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favourite_routes');
    }
};
