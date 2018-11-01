<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuoteItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quote_id')->index();

            // Product
            $table->unsignedInteger('product_id')->index()->nullable(true);
            $table->string('product_name')->nullable(true);
            $table->float('product_price')->nullable(true);
            $table->string('product_qty')->nullable(true);


            // Prices
            $table->float('subtotal')->default(0);
            $table->float('total')->default(0);

            $table->longText('attributes')->nullable(true);
            $table->timestamps();
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->foreign('quote_id')->references('id')->on('quotes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quote_items');
    }
}
