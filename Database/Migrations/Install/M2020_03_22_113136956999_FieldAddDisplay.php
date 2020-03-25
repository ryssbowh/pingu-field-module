<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M2020_03_22_113136956999_FieldAddDisplay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'display_fields', function (Blueprint $table) {
                $table->increments('id');
                $table->string('object');
                $table->string('field');
                $table->unsignedInteger('label');
                $table->boolean('hidden')->default(0);
                $table->string('displayer');
                $table->json('options');
                $table->unsignedInteger('weight');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('display_fields');
    }
}
