<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M2019_10_01_182836436202_InstallField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundle_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('machineName');
            $table->string('helper');
            $table->string('bundle');
            $table->integer('weight');
            $table->integer('cardinality');
            $table->boolean('deletable');
            $table->boolean('editable');
            $table->morphs('instance');
            $table->timestamps();
        });

        Schema::create('bundle_field_values', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('value')->nullable();
            $table->morphs('entity');
            $table->integer('field_id')->unsigned();
            $table->foreign('field_id')->references('id')->on('bundle_fields')->onDelete('cascade');
            $table->integer('revision_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('field_booleans', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('default');
            $table->timestamps();
        });

        Schema::create('field_datetimes', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('required');
            $table->boolean('setToCurrent');
            $table->timestamps();
        });

        Schema::create('field_date', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('required');
            $table->boolean('setToCurrent');
            $table->timestamps();
        });

        Schema::create('field_emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('default');
            $table->boolean('required');
            $table->timestamps();
        });

        Schema::create('field_integers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('default');
            $table->boolean('required');
            $table->integer('maxLength');
            $table->timestamps();
        });

        Schema::create('field_floats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('precision');
            $table->string('default');
            $table->timestamps();
        });

        Schema::create('field_texts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('default');
            $table->boolean('required');
            $table->integer('maxLength')->unsigned();
            $table->timestamps();
        });

        Schema::create('field_text_longs', function (Blueprint $table) {
            $table->increments('id');
            $table->text('default');
            $table->boolean('required');
            $table->timestamps();
        });

        Schema::create('field_urls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('default');
            $table->boolean('required');
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
        Schema::dropIfExists('bundle_fields_values');
        Schema::dropIfExists('bundle_fields');
        Schema::dropIfExists('field_urls');
        Schema::dropIfExists('field_texts');
        Schema::dropIfExists('field_text_longs');
        Schema::dropIfExists('field_integers');
        Schema::dropIfExists('field_floats');
        Schema::dropIfExists('field_emails');
        Schema::dropIfExists('field_datetimes');
        Schema::dropIfExists('field_booleans');
        Schema::dropIfExists('field_slugs');
    }
}
