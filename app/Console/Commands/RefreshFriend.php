<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Http\Controllers\SteamDataController;

class RefreshFriend extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'refresh:friend';
    protected $steamData;

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh all player friends.';

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
        $this->steamData->refreshPlayerFriendList();
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
