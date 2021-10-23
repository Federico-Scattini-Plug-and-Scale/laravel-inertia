<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableJobOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description');
            $table->json('tech_stack');
            $table->text('terms');

            $table->enum('experience_level', [
                'junior',
                'mid',
                'senior'
            ]);
            $table->enum('work_mode', [
                'on_site',
                'hybrid',
                'fully_remote'
            ]);
            $table->enum('status', [
                'open',
                'close'
            ]);
            $table->enum('interview_type',[
                'on_site',
                'online'
            ]);

            $table->integer('min_salary')->nullable();
            $table->integer('max_salary')->nullable();

            $table->foreignId('company_id')->constrained('users');

            $table->boolean('apply_externally')->default(0);
            $table->string('application_link')->nullable();

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
        Schema::dropIfExists('job_offers');
    }
}
