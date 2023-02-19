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
        Schema::table('favourite_visits', function (Blueprint $table) {
            $table->foreign(['visit_id'], 'favourite_visits_ibfk_2')->references(['place_id'])->on('visits');
            $table->foreign(['user_id'], 'favourite_visits_ibfk_1')->references(['id'])->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favourite_visits', function (Blueprint $table) {
            $table->dropForeign('favourite_visits_ibfk_2');
            $table->dropForeign('favourite_visits_ibfk_1');
        });
    }
};
