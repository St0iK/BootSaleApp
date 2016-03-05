<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = public_path().'/images/ads';
        File::makeDirectory($path, $mode = 0777, true, true);
        Schema::create('ad_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ad_id');
            $table->string('title');
            $table->string('description');
            $table->string('path');
            $table->string('thumb_path');
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
        Schema::drop('ad_photos');
    }
}
