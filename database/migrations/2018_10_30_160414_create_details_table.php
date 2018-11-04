<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->increments('id');
			$table->string('printkey', 30);
			$table->string('street_number', 25);
			$table->string('street_name', 250);
			$table->string('municipality', 50);
			$table->decimal('acres', 10, 4);
			$table->string('tax_id', 25);
			$table->string('neighborhood_code', 50);
			$table->decimal('fmv_value', 10, 2);
			$table->integer('fmv_year');
			$table->integer('square_footage');
			$table->integer('stories');
			$table->integer('bedrooms');
			$table->integer('full_baths');
			$table->integer('half_baths');
			$table->integer('year_built');
			$table->integer('swis_code');
			$table->integer('class_code');
			$table->string('class_description', 250);
			$table->string('description', 250);
			$table->string('report_url', 300);
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
        Schema::dropIfExists('details');
    }
}
