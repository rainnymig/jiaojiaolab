<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlaytimeToSteamusers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('steamusers', function(Blueprint $table)
		{
			//
            $table->integer('allplaytimeforever');
            $table->integer('allplaytime2weeks');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('steamusers', function(Blueprint $table)
		{
			//
		});
	}

}
