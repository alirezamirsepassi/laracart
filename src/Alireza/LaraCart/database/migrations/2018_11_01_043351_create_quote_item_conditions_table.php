<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuoteItemConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_item_conditions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('quote_item_id');
            $table->string('name')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('value')->nullable(true);
            $table->float('order')->default(1)->nullable();

            $table->longText('attributes')->nullable(true);
            $table->timestamps();
        });

        Schema::table('quote_item_conditions', function (Blueprint $table) {
            $table->foreign('quote_item_id')->references('id')->on('quote_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_item_conditions');
    }
}
