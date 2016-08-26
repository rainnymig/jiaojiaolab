<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SteamGameController extends Controller {

	//

    public function showPlayerGameGalary($steamid)
    {
        return view('gamegalary')->with('steamid', $steamid);
    }

}
