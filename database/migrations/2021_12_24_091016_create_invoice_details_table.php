<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_name');
            $table->string('invoice_street');
            $table->string('invoice_city');
            $table->string('invoice_postal_code');
            $table->string('invoice_country');
            $table->string('invoice_vat_number');
            $table->string('invoice_phone');
            $table->string('invoice_email');
            $table->boolean('is_completed')->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

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
        Schema::dropIfExists('invoice_details');
    }
}
