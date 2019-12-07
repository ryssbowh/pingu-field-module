<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M2019_12_02_220302807791_FieldAddRevisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'revisions', function (Blueprint $table) {
                $table->increments('id');
                $table->longText('value')->nullable();
                $table->unsignedInteger('revision');
                $table->morphs('model');
                $table->string('field');
                $table->createdBy();
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
        Schema::dropIfExists('revisions');
    }
}
