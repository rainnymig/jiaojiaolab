<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Steamuser extends Model {

	//
    protected $fillable = ['personaname', 'steamid', 'avatarimage', 
        'recentplayedgame1', 'recentplayedimage1', 'recentplayedgame2', 'recentplayedimage2', 'recentplayedgame3', 'recentplayedimage3',
        'badgeimage1', 'badgeimage2', 'badgeimage3', 'badgeimage4',
        'playerlevel', 'playergamecount', 'playerfriendcount', 'playerbadgecount',
        'expiretime',
        'playercardimage', 'backgroundImage',
        'playerxp', 'playerxpneed', 'playerxpcurrent',
        'gamecount', 'allplaytime2weeks', 'allplaytimeforever'];

    //  返回 steamusers 表中某个玩家的 'recentplayedgame1' 是否设置
    static function issetRecentplayed($steamid)
    {
        $result = Steamuser::where('steamid', $steamid)->first();
        if(null == $result)
        {
            return 'user not exist!';
        }
        else
        {
            $result = $result->toArray();
            if($result['recentplayedgame1']=='')
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;
    }

    static function issetBadgeList($steamid)
    {
        $result = Steamuser::where('steamid', $steamid)->first();
        if(null == $result)
        {
            return 'user not exist!';
        }
        else
        {
            $result = $result->toArray();
            if($result['badgeimage1']=='')
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;
    }

    //  比较对应玩家的 expiretime 与当前时间
    //  判断该玩家的资料是否需要刷新
    //  若 expiretime 比 Carbon::now() 早则说明已过期，返回 true
    //  否则返回 false
    static function isExpired($steamid)
    {
        $result = Steamuser::where('steamid', $steamid)->first();
        if(null == $result)
        {
            return 'user not exist!';
        }
        else
        {
            $result = $result->toArray();
            if($result['expiretime'] < Carbon::now())
            {
                return true;
            }
            else 
            {
                return false;
            };
        }
        return true;
    }

    //  为用户信息设置过期时间
    //  设置为当前时间+30分钟
    static function setExpire($steamid)
    {
        $result = Steamuser::where('steamid', $steamid)->first();
        $result->expiretime = Carbon::now()->addMinutes(30);
        $result->save();
    }
}
