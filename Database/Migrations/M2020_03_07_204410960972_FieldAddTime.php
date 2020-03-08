<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M2020_03_07_204410960972_FieldAddTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_datetimes', function (Blueprint $table) {
            $table->string('format');
        });
        Schema::create(
            'field_times', function (Blueprint $table) {
                $table->increments('id');
                $table->boolean('required');
                $table->boolean('setToCurrent');
                $table->string('format');
                $table->timestamps();
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
        Schema::dropIfExists('field_times');
        Schema::table('field_datetimes', function (Blueprint $table) {
            $table->dropColumn('format');
        });
    }
}
