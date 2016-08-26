<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Steamfriend extends Model {

	//
    protected $fillable = ['steamid', 'personaname', 'friendname', 'friendavatar', 'friendsteamid'];

    static function isExpired($steamid)
    {
        $results = Steamfriend::select('expiretime')->where('steamid', $steamid)->get();
        if(count($results) == 0)
        {
            return true;
        }
        else
        {
            foreach($results as $thetime)
            {
                if($thetime['expiretime'] < Carbon::now())
                {
                    return true;
                }
            }
            return false;

            
        }
        return true;
    }

    //  为好友列表设置过期时间
    //  设置为当前时间+1天
    //  **目前暂时不用这个函数
    //static function setExpire($steamid)
    //{
    //    $results = Steamfriend::where('steamid', $steamid)->get();
    //    foreach($results as $result)
    //    {
    //        $result->expiretime = Carbon::now()->addDays(1);
    //        $result->save();
    //    }
    //}

    //  清除指定玩家的所有朋友（为了重新刷新）
    static function clearAllFriendData($steamid)
    {
        $results = Steamfriend::where('steamid', $steamid)->get();
        foreach($results as $result)
        {
            $result->delete();
        }
    }
}
