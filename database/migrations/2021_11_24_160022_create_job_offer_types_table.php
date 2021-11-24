<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOfferTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offer_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('stripe_product_name');
            $table->integer('rank')->default(0);
            $table->string('currency');
            $table->bigInteger('price');
            $table->text('stripe_product_id');
            $table->text('stripe_price_id');
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
        Schema::dropIfExists('job_offer_types');
    }
}
