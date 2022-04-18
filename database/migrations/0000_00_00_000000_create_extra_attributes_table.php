<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('extra_attributes')) {
            Schema::create('extra_attributes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('model_id')->index();
                $table->string('model_type')->index();
                $table->json('attrs');
                $table->timestamps();

                $table->unique(['model_id', 'model_type']);
            });
        }
    }
}
