<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->string('contact_name')->after('website_link');
            $table->string('phone')->after('contact_name');
            $table->text('description')->nullable()->after('name');
            $table->boolean('is_agency')->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'phone', 'description', 'is_agency']);
        });
    }
}
