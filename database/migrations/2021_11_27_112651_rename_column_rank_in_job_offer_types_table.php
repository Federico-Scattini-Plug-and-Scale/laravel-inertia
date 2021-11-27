<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnRankInJobOfferTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('job_offer_types', function (Blueprint $table) {
            $table->renameColumn('rank', 'ranking');
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
            $table->renameColumn('ranking', 'rank');
        });
    }
}
