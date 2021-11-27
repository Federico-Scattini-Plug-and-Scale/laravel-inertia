<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMakeNullableColumnsInJobOfferTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_offer_types', function (Blueprint $table) {
            $table->string('stripe_product_name')->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->bigInteger('price')->nullable()->change();
            $table->text('stripe_product_id')->nullable()->change();
            $table->text('stripe_price_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_offer_types', function (Blueprint $table) {
            $table->string('stripe_product_name')->nullable(false)->change();
            $table->string('currency')->nullable(false)->change();
            $table->bigInteger('price')->nullable(false)->change();
            $table->text('stripe_product_id')->nullable(false)->change();
            $table->text('stripe_price_id')->nullable(false)->change();
        });
    }
}
