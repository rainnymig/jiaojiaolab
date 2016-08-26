<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use LightOpenID;
use App\Functions\ToolFunction;
use App\Steamuser;
use Session;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Request;
//use Request;

class SteamHomeController extends Controller {

	//
    private $apiKey = '57EAA0477252960EDFB7AE4FF2BAA1C0';

    public function showHomepage()
    {
        return view('homepage');
    }

    public function loginWithSteam(Request $request)
    {
        try
        {
            $openId = new LightOpenID('http://rainnymiglab.cc');
            if(!$openId->mode)
            {
                if(isset($_GET['login']))
                {
                    $openId->identity = 'http://steamcommunity.com/openid/?l=english';
                    header('Location: '.$openId->authUrl());
                }
                else
                {
                    echo 'error';
                }
            }
            elseif($openId->mode == 'cancel')
            {
                echo 'User has cancelled authentication';
            }
            else
            {
                if($openId->validate())
                {
                    $id = $openId->identity;
                    $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                    preg_match($ptn, $id, $matches);
                    $steamid = $matches[1];
                    
                    return redirect()->route('profile', ['steamid'=>$steamid]);
                }
                else
                {
                    echo 'User is not login.';
                }
            }
        }
        catch(exception $e)
        {
            echo $e->getMessage();
        }
    }

    public function userLogout()
    {
        if(Session::has('loginUser'))
        {
            Session::forget('loginUser');
            Session::forget('loginUserAvatar');
            Session::forget('loginUserSteamId');
            return redirect()->route('getHomepage');
        }
    }

}
