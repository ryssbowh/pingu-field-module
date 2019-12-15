<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M2019_12_07_103101322608_FieldAddLayout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'form_layout_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('object');
                $table->unsignedInteger('weight')->default(0);
            }
        );

        Schema::create(
            'form_layouts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('object');
                $table->string('field');
                $table->string('widget');
                $table->json('options');
                $table->unsignedInteger('group_id')->nullable();
                $table->foreign('group_id')->references('id')->on('form_layout_groups')->onDelete('set null');
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
        //
    }
}
