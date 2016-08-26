<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\SteamDataController;
use App\Http\Controllers\PlayerCardController;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
        'App\Console\Commands\RefreshData',
        'App\Console\Commands\RefreshFriend',
        'App\Console\Commands\RefreshCard',
        'App\Console\Commands\RefreshGame',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('inspire')
				 ->hourly();
        //  定时刷新玩家资料存入数据库
        $schedule->command('refresh:data')->everyThirtyMinutes(); 
        $schedule->command('refresh:card')->everyThirtyMinutes(); 
        $schedule->command('refresh:game')->daily(); 
        $schedule->command('refresh:friend')->daily(); 
        //$schedule->command('refresh:friend')->cron('* * */1 * *'); 
        //$schedule->command('refresh:data')->cron('*/30 * * * *'); 
        //$schedule->command('refresh:card')->cron('*/30 * * * *'); 
	}

}
