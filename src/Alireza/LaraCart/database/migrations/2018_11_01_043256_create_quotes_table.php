<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->string('id')->unique();

            // Customer information
            $table->unsignedInteger('customer_id')->nullable(true);
            $table->string('customer_email')->nullable(true);
            $table->string('customer_phone')->nullable(true);
            $table->string('customer_firstname')->nullable(true);
            $table->string('customer_lastname')->nullable(true);

            // Prices
            $table->float('subtotal')->default(0);
            $table->float('total')->default(0);

            $table->longText('attributes')->nullable(true);
            $table->dateTime('converted_at')->nullable(true);
            $table->timestamps();
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->primary(['id']);
            $table->index(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
