<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Steamgame extends Model {
    
	//
    protected $fillable = ['steamid', 'gamename', 'appid', 
                        'playtime2weeks', 'playtimeforever',
                        'gameicon', 'gamelogo'];


    //  清除指定玩家的所有游戏（为了重新刷新）
    static function clearAllGameData($steamid)
    {
        $results = Steamgame::where('steamid', $steamid)->get();
        foreach($results as $result)
        {
            $result->delete();
        }
    }

    //  检查玩家是否有游戏，没有的话返回需要刷新
    static function hasGame($steamid)
    {
        $result = Steamgame::where('steamid', $steamid)->first();
        if(null == $result)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
