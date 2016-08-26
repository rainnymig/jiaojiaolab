<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Http\Controllers\SteamDataController;

class RefreshData extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'refresh:data';
    protected $steamData;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh all player data.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(SteamDataController $steamData)
	{
		parent::__construct();
        $this->steamData = $steamData;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
        $this->steamData->refreshPlayerData();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
    /*
	protected function getArguments()
	{
		return [
			['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}
     */

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
    /*
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}
     */

}
