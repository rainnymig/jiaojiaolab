<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLevelToSteamusers extends Migration {

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
            $table->integer('playerxp');
            $table->integer('playerxpneed');
            $table->integer('playerxpcurrent');
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
