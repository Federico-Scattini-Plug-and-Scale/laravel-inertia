<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnLocaleToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_offer_types', function (Blueprint $table) {
            $table->string('locale')->nullable();
        });

        Schema::table('job_offers', function (Blueprint $table) {
            $table->string('locale')->nullable();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->string('locale')->nullable();        
        });

        Schema::table('tag_groups', function (Blueprint $table) {
            $table->string('locale')->nullable();        
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('locale')->nullable();
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
            $table->dropColumn('locale');
        });

        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('tag_groups', function (Blueprint $table) {
            $table->dropColumn('locale');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
}
