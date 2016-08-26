<?php namespace App\Http\Controllers;

use App\Http\Requests;
use Sunra\PhpSimple\HtmlDomParser;
use App\Http\Controllers\Controller;
use App\Functions\ToolFunction;
use Session;
use Carbon\Carbon;

use App\Steamfriend;
use App\Steamuser;
use App\Steamgame;

use Illuminate\Http\Request;

class SteamDataController extends Controller {

	//

    private $apiKey = '57EAA0477252960EDFB7AE4FF2BAA1C0';

    public function dbTest()
    {
        $currentGames = Steamgame::select('appid')->where('steamid', '76561198078931851')->get();
        $currentGames = $currentGames->toArray();
        if(!in_array(array('appid'=>'223750'), $currentGames))
        {
            echo 'not in';
        }
        else
        {
            echo 'in';
        }
    }

    public function localDataTest()
    {
        $steamUserInfo = unserialize(file_get_contents('steamUserInfo'));
        $steamFriendList = unserialize(file_get_contents('steamFriendList'));
        $steamOwnedGameCount = unserialize(file_get_contents('steamOwnedGameCount'));
        $steamOwnedGame = unserialize(file_get_contents('steamOwnedGame'));
        $steamRecentPlayedCount = unserialize(file_get_contents('steamRecentPlayedCount'));
        $steamRecentPlayed = unserialize(file_get_contents('steamRecentPlayed'));

        $steamUserInfo = $steamUserInfo[0];
        return view('playerprofile')->with(['steamUserInfo'=>$steamUserInfo, 'steamFriendList'=>$steamFriendList,
        									'steamOwnedGameCount'=>$steamOwnedGameCount, 'steamOwnedGame'=>$steamOwnedGame,
           									'steamRecentPlayedCount'=>$steamRecentPlayedCount, 'steamRecentPlayed'=>$steamRecentPlayed]);
    }

    //  获取指定玩家所拥有的游戏
    //  由于游戏是分页显示的，所以 gamepage 参数显示现在是第几页
    //  由此返回相应的游戏
    public function getPlayerOwnedGame($steamid, $refresh, $gamepage, $displaycount)
    {
        $refreshFlag = false;

        if($refresh == 'refresh')
        {
            $refreshFlag = true;
        }
        elseif(false == Steamgame::hasGame($steamid))
        {
            $refreshFlag = true;
        }

        if($refreshFlag == true)
        {
            $this->getPlayerOwnedGameActual($steamid);
        }

        $queryResult = Steamuser::select('gamecount')->where('steamid', $steamid)->first();
        $playerGameCount = $queryResult['gamecount'];
        if(($gamepage*$displaycount)>$playerGameCount)
        {
            return 0;
        }
        else
        {
            $playerOwnedGame = array();
            $queryResult = Steamgame::select('appid', 'gamename', 'playtime2weeks', 'playtimeforever')->where('steamid', $steamid)->skip($displaycount*$gamepage)->take($displaycount)->get();
            if(null != $queryResult)
            {
                $playerOwnedGame = $queryResult->toArray();
            }
            $playerGameInfo = array('gameCount'=>$playerGameCount, 'gameList'=>$playerOwnedGame);
            /*
            echo '<pre>';
            print_r($playerGameInfo);
            echo '</pre>';
             */
            return json_encode($playerGameInfo);
        }
        return 0;
    }

    private function getPlayerOwnedGameActual($steamid)
    {
        $requestUrl = 'http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key='.($this->apiKey).'&steamid='.$steamid.'&include_appinfo=1&format=json';
        $steamGameJson = file_get_contents($requestUrl);
        $steamGame = json_decode($steamGameJson, true);
        $steamOwnedGame = $steamGame['response'];

        //Steamgame::clearAllGameData($steamid);
        $currentGames = Steamgame::select('appid')->where('steamid', $steamid)->get();
        if(null != $currentGames)
        {
            $currentGames = $currentGames->toArray();
        }
        else
        {
            $currentGames = array();
        }

        $playtimeForeverSum = 0;
        $playtime2WeeksSum = 0;

        foreach($steamOwnedGame['games'] as $game)
        {
            //  先判断是否数据库中已存在这个游戏
            //  若已存在则不用再插入
            //  若不存在则说明是新购买的，要创建新元素
            //  因为 $currentGames 是一个数组的数组，
            //  其每个元素为一个索引数组，其实只有一个键值对（'appid'=>xxxx)
            //  因此需要构造一个数组和它每个元素比较
            if(!in_array(array('appid'=>$game['appid']), $currentGames))
            {
                $gameResult = Steamgame::create(['steamid'=>$steamid, 'gamename'=>$game['name'], 'appid'=>$game['appid'], 'playtimeforever'=>$game['playtime_forever'], 'gamelogo'=>$game['img_logo_url'], 'gameicon'=>$game['img_icon_url']]);
                if(isset($game['playtime_2weeks']))
                {
                    $gameResult->playtime2weeks = $game['playtime_2weeks'];
                }
                else
                {
                    $gameResult->playtime2weeks = 0;
                }
                $gameResult->save();
            }
            $playtimeForeverSum += $game['playtime_forever'];
            if(isset($game['playtime_2weeks']))
            {
                $playtime2WeeksSum += $game['playtime_2weeks'];
            }
            //  playtimeforever 和 playtime2weeks 其实都是以分钟计算
        }

        $user = Steamuser::where('steamid', $steamid)->first();
        $user->gamecount = $steamOwnedGame['game_count'];
        $user->allplaytimeforever = $playtimeForeverSum;
        $user->allplaytime2weeks = $playtime2WeeksSum;
        $user->save();
    
    }

    //  获取指定玩家所拥有的游戏之中随机的若干个
    public function getPlayerRandomGame($steamid, $requireCount)
    {
        $refreshFlag = false;

        if(false == Steamgame::hasGame($steamid))
        {
            $refreshFlag = true;
        }

        if($refreshFlag == true)
        {
            $this->getPlayerOwnedGameActual($steamid);
        }

        $queryResult = Steamuser::select('gamecount')->where('steamid', $steamid)->first();
        $user = $queryResult->toArray(); 
        if($requireCount < $user['gamecount'])
        {
            $skipCount = mt_rand(0, $user['gamecount']-$requireCount); 
            //echo $skipCount;
            $randomGames = Steamgame::where('steamid', $steamid)->skip($skipCount)->take($requireCount)->select('appid', 'gamename', 'gamelogo')->get();
            $randomGames = $randomGames->toArray();
            return json_encode($randomGames);
            //echo '<pre>';
            //print_r($randomGames);
            //echo '</pre>';
        }
        elseif($user['gamecount'] > 0)
        {
            $randomGames = Steamgame::select('appid', 'gamename', 'gamelogo')->where('steamid', $steamid)->get();
            $randomGames = $randomGames->toArray();
            return json_encode($randomGames);
            //echo '<pre>';
            //print_r($randomGames);
            //echo '</pre>';
        }
        else
        {
            return 0;
        }

    }

    //  获取指定玩家最近玩过的游戏
    public function getPlayerRecentPlayed($steamid, $refresh)
    {
        //  是否需要刷新的标志位
        //  满足以下条件的话则需要请求 steam 服务器刷新数据
        //  否则直接读本地数据库内容返回即可
        //  1   $refresh == 'refresh'
        //  2   数据库里面没有该用户最近游玩的游戏的记录
        //  3   数据库里有该用户最近又玩游戏的记录，但是已经过期
        //  若要刷新，则要调用 getPlayerRecentPlayedActual 函数
        //  那个才是实际查询玩家数据的函数
        $refreshFlag = false;

        if($refresh == 'refresh')
        {
            $refreshFlag = true;
        }
        else if(false == Steamuser::issetRecentplayed($steamid))
        {
            $refreshFlag = true;
        }
        else if(true == Steamuser::isExpired($steamid))
        {
            $refreshFlag = true;
        }

        if($refreshFlag == true)
        {
            $this->getPlayerRecentPlayedActual($steamid);
        }
        
    
        $steamRecentPlayed = Steamuser::select('recentplayedgame1', 'recentplayedappid1', 'recentplayedimage1', 'recentplayedicon1',
                                    'recentplayedgame2', 'recentplayedappid2', 'recentplayedimage2', 'recentplayedicon2',
                                    'recentplayedgame3', 'recentplayedappid3', 'recentplayedimage3', 'recentplayedicon3')->where('steamid', $steamid)->first();
        if(null != $steamRecentPlayed)
        {
            $steamRecentPlayed = $steamRecentPlayed->toArray();
            $steamRecentPlayedJson = json_encode($steamRecentPlayed);
            /*
            echo '<pre>';
            print_r($steamRecentPlayed);
            echo '</pre>';
             */
            return $steamRecentPlayedJson;
        }
    }

    private function getPlayerRecentPlayedActual($steamid)
    {
        $requestUrl = 'http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key='.($this->apiKey).'&steamid='.$steamid.'&count=3&format=json';
        $steamRecentPlayedJson = file_get_contents($requestUrl);
        $steamGame = json_decode($steamRecentPlayedJson, true);
        $steamRecentPlayed=$steamGame['response'];

        $queryResult = Steamuser::where('steamid', $steamid)->first();
        if($steamRecentPlayed['total_count'] == 0)
        {

        }
        if($steamRecentPlayed['total_count'] >= 1)
        {
            $queryResult->recentplayedgame1 = $steamRecentPlayed['games'][0]['name'];
            $queryResult->recentplayedappid1 = $steamRecentPlayed['games'][0]['appid'];
            $queryResult->recentplayedimage1 = 'http://media.steampowered.com/steamcommunity/public/images/apps/'.$steamRecentPlayed['games'][0]['appid'].'/'.$steamRecentPlayed['games'][0]['img_logo_url'].'.jpg';
            $queryResult->recentplayedicon1 = 'http://media.steampowered.com/steamcommunity/public/images/apps/'.$steamRecentPlayed['games'][0]['appid'].'/'.$steamRecentPlayed['games'][0]['img_icon_url'].'.jpg';
        }
        if($steamRecentPlayed['total_count'] >= 2)
        {
            $queryResult->recentplayedgame2 = $steamRecentPlayed['games'][1]['name'];
            $queryResult->recentplayedappid2 = $steamRecentPlayed['games'][1]['appid'];
            $queryResult->recentplayedimage2 = 'http://media.steampowered.com/steamcommunity/public/images/apps/'.$steamRecentPlayed['games'][1]['appid'].'/'.$steamRecentPlayed['games'][1]['img_logo_url'].'.jpg';
            $queryResult->recentplayedicon2 = 'http://media.steampowered.com/steamcommunity/public/images/apps/'.$steamRecentPlayed['games'][1]['appid'].'/'.$steamRecentPlayed['games'][1]['img_icon_url'].'.jpg';
        }
        if($steamRecentPlayed['total_count'] >= 3)
        {
            $queryResult->recentplayedgame3 = $steamRecentPlayed['games'][2]['name'];
            $queryResult->recentplayedappid3 = $steamRecentPlayed['games'][2]['appid'];
            $queryResult->recentplayedimage3 = 'http://media.steampowered.com/steamcommunity/public/images/apps/'.$steamRecentPlayed['games'][2]['appid'].'/'.$steamRecentPlayed['games'][2]['img_logo_url'].'.jpg';
            $queryResult->recentplayedicon3 = 'http://media.steampowered.com/steamcommunity/public/images/apps/'.$steamRecentPlayed['games'][2]['appid'].'/'.$steamRecentPlayed['games'][2]['img_icon_url'].'.jpg';
        }

        $queryResult->save();

    }

    //  获取指定玩家的好友列表
    public function getFriendList($steamid, $refresh)
    {
        $refreshFlag = false;

        if($refresh == 'refresh')
        {
            $refreshFlag = true;
        }
        else if(true == Steamfriend::isExpired($steamid))
        {
            $refreshFlag = true;
        }

        if($refreshFlag == true)
        {
            $this->getFriendListActual($steamid);
        }

        $friendAvatar = array();
        $friendName = array();
        $queryResult = Steamfriend::select('friendname', 'friendavatar')->where('steamid', $steamid)->get();
        if(null != $queryResult)
        {
            $queryResult = $queryResult->toArray();
            foreach($queryResult as $item)
            {
                $friendAvatar[]=$item['friendavatar'];
                $friendName[]=$item['friendname'];
            }
        }
        
        $steamFriendList = array('friendAvatar'=>$friendAvatar, 'friendName'=>$friendName);
        $steamFriendList = json_encode($steamFriendList);
        /*
        echo '<pre>';
        print_r($steamFriendList);
        echo '</pre>';
         */
        return $steamFriendList;
    }

    public function getFriendListActual($steamid)
    {
        $requestUrl = 'http://steamcommunity.com/profiles/'.$steamid.'/friends';
        $result = file_get_contents($requestUrl);
        $playFriendHtml = HtmlDomParser::str_get_html($result);
        $resAvatar = $playFriendHtml->find('.playerAvatar img');
        $resName = $playFriendHtml->find('.friendBlockContent');
        //$friendAvatar = array();
        //$friendName = array();

        $currentFriends = Steamfriend::select('friendname')->where('steamid', $steamid)->get();
        if(null != $currentFriends)
        {
            $currentFriends = $currentFriends->toArray();
        }
        else
        {
            $currentFriends = array();
        }
        
        $i = 1;
        foreach($resName as $nam)
        {
            $theAvatar = $resAvatar[$i]->src;
            $i++;
            $rawData = trim($nam->innertext);
            $theName = strstr($rawData, '<', true);
            if(!in_array(array('friendname'=>$theName), $currentFriends))
            {
                $theFriend = Steamfriend::create(['steamid'=>$steamid, 'friendname'=>$theName, 'friendavatar'=>$theAvatar]);
                $theFriend->expiretime = Carbon::now()->addDay();
                $theFriend->save();
            }
            //$friendName[]=strstr($rawData, '<', true);
        }
        $queryResult = Steamuser::where('steamid', $steamid)->first();
        $queryResult->playerfriendcount = count($resName);
        $queryResult->save();
    }

    //  获取指定玩家的展示徽章
    public function getBadgeList($steamid, $refresh)
    {
        $refreshFlag = false;

        if($refresh == 'refresh')
        {
            $refreshFlag = true;
        }
        else if(false == Steamuser::issetBadgeList($steamid))
        {
            $refreshFlag = true;
        }
        else if(true == Steamuser::isExpired($steamid))
        {
            $refreshFlag = true;
        }

        $badgeImage = array();
        if($refreshFlag == true)
        {
            $this->getBadgeListActual($steamid);
        }
        $steamBadge = Steamuser::select('badgeimage1', 'badgeimage2', 'badgeimage3', 'badgeimage4', 'playerbadgecount')->where('steamid', $steamid)->first();
        if(null != $steamBadge)
        {
            $steamBadgeJson = json_encode($steamBadge);
            /*
            echo '<pre>';
            print_r($steamBadge);
            echo '</pre>';
             */
            return($steamBadgeJson);

        }
    }

    public function getBadgeListActual($steamid)
    {
        $requestUrl = 'http://steamcommunity.com/profiles/'.$steamid;
        $result = file_get_contents($requestUrl);
        $playBadgeHtml = HtmlDomParser::str_get_html($result);
        $resImage = $playBadgeHtml->find('.profile_badges_badge a img');
        foreach($resImage as $ima)
        {
            $badgeImage[]=$ima->src;
        }

        $requestUrl = 'https://api.steampowered.com/IPlayerService/GetBadges/v1?key='.$this->apiKey.'&steamid='.$steamid;
        $result = file_get_contents($requestUrl);
        $playerBadgeData = json_decode($result, true);
        $queryResult = Steamuser::where('steamid', $steamid)->first();
        if(isset($playerBadgeData['response']['badges']))
        {
            $badgeCount = count($playerBadgeData['response']['badges']);

            $queryResult->playerbadgecount = $badgeCount;
            if($badgeCount>=1)
            {
                $queryResult->badgeimage1 = $badgeImage[0];
            }
            if($badgeCount>=2)
            {
                $queryResult->badgeimage2 = $badgeImage[1];
            }
            if($badgeCount>=3)
            {
                $queryResult->badgeimage3 = $badgeImage[2];
            }
            if($badgeCount>=4)
            {
                $queryResult->badgeimage4 = $badgeImage[3];
            }

        }
        $queryResult->playerlevel = $playerBadgeData['response']['player_level'];
        $queryResult->playerxp = $playerBadgeData['response']['player_xp'];
        $queryResult->playerxpneed = $playerBadgeData['response']['player_xp_needed_to_level_up'];
        $queryResult->playerxpcurrent = $playerBadgeData['response']['player_xp_needed_current_level'];
        $queryResult->save();
    } 

    //  获取指定玩家的统计数据
    //  目前包括：游戏历史游玩时间 游戏近两周游玩时间 游戏数
    public function getPlayerStat($steamid, $refresh)
    {
        $queryResult = Steamuser::select('gamecount', 'allplaytimeforever', 'allplaytime2weeks')->where('steamid', $steamid)->first();
        $statGeneral = $queryResult->toArray();
        $statDetail = array();
        if($statGeneral['gamecount'] != 0)
        {
            $queryResult = Steamgame::where('steamid', $steamid)->orderby('playtimeforever', 'desc')->select('gamename', 'playtimeforever')->take(6)->get();
            $statDetailGtForever = $queryResult->toArray();
            $timeSum = 0;
            if($statGeneral['gamecount'] > 6 && $statDetailGtForever[5]['playtimeforever'] != 0)
            {
                foreach($statDetailGtForever as $GtForever)
                {
                    $timeSum += $GtForever['playtimeforever'];
                }
                $otherTime = $statGeneral['allplaytimeforever']-$timeSum;
                $statDetailGtForever[]=array('gamename'=>'Others', 'playtimeforever'=>$otherTime);
            }
            $queryResult = Steamgame::where('steamid', $steamid)->orderby('playtime2weeks', 'desc')->select('gamename', 'playtime2weeks')->take(6)->get();
            $statDetailGt2Weeks = $queryResult->toArray();
            $timeSum = 0;
            if($statGeneral['gamecount'] >6 && $statDetailGt2Weeks[5]['playtime2weeks'] != 0)
            {
                foreach($statDetailGt2Weeks as $Gt2Weeks)
                {
                    $timeSum += $Gt2Weeks['playtime2weeks'];
                }
                $otherTime = $statGeneral['allplaytime2weeks']-$timeSum;
                $statDetailGt2Weeks[]=array('gamename'=>'Others', 'playtime2weeks'=>$otherTime);
            }
            $playerStat = array('statGeneral'=>$statGeneral, 'statDetailGtForever'=>$statDetailGtForever, 'statDetailGt2Weeks'=>$statDetailGt2Weeks);
        }
        else
        {
            $playerStat = array('statGeneral'=>$statGeneral, 'statDetailGtForever'=>0, 'statDetailGt2Weeks'=>0);
        }
        //echo '<pre>';
        //print_r($playerStat);
        //echo '</pre>';
        $playerStatJson = json_encode($playerStat);
        return $playerStatJson;
    }


    //  获取指定玩家的背景图片
    public function getBackgroundImage($steamid)
    {
        $queryResult = Steamuser::select('backgroundImage')->where('steamid', $steamid)->first()->toArray();
        if($queryResult['backgroundImage'] != '')
        {
            return $queryResult['backgroundImage'];
        }
        else
        {
            $requestUrl = 'http://steamcommunity.com/profiles/'.$steamid;
            $result = file_get_contents($requestUrl);
            $playBackgroundHtml = HtmlDomParser::str_get_html($result);
            $rawData = $playBackgroundHtml->find('.no_header');
            if($rawData[0]->style != '')
            {
                $steamBackground = $rawData[0]->style;
                $steamBackground = strstr($steamBackground, 'h');
                $steamBackground = strstr($steamBackground, '\'', true);
            }
            else
            {
                $steamBackground = '';
            }
            $queryResult = Steamuser::where('steamid', $steamid)->first();
            $queryResult->backgroundImage = $steamBackground;
            $queryResult->save();
            return $steamBackground;
        }
    }

    public function getBasicInfo($steamid)
    {
        $queryResult = Steamuser::select('steamid', 'personaname', 'avatarImage', 'playerlevel')->where('steamid', $steamid)->first()->toArray();
        return $queryResult;
    }

    //  返回指定用户的个人主页面
    public function showPlayerProfile($steamid)
    {
        $requestUrl = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$this->apiKey.'&steamids='.$steamid;
        $steamUserInfoJson = file_get_contents($requestUrl);
        $steamUserInfo = json_decode($steamUserInfoJson, true);
        $personaname = $steamUserInfo['response']['players'][0]['personaname'];
        $avatarimage = $steamUserInfo['response']['players'][0]['avatarfull'];

        $queryResult = Steamuser::firstOrCreate(['steamid'=>$steamid]);
        $queryResult->personaname = $personaname;
        $queryResult->avatarImage = $avatarimage;
        //$queryResult->expiretime = Carbon::now();      //  China Beijing time is GMT+8
        $queryResult->save();

        if('nologin'==Session::get('loginUser', 'nologin'))
        {
            Session::put('loginUser', $personaname);
            Session::put('loginUserSteamId', $steamid);
            Session::put('loginUserAvatar', $avatarimage);
        }

        $requestUrl = 'http://api.steampowered.com/IPlayerService/GetBadges/v1?key='.$this->apiKey.'&steamid='.$steamid;
        $steamUserBadgeJson = file_get_contents($requestUrl);
        $steamUserBadge = json_decode($steamUserBadgeJson, true);
        $steamUserBadge = $steamUserBadge['response'];
        $steamUserInfo = $steamUserInfo['response']['players'][0];
        return view('playerprofile')->with(['steamUserInfo'=>$steamUserInfo, 
                                            'steamUserBadge'=>$steamUserBadge
                                           ]);
    }

    //  刷新用户数据库内容
    //  用于定时任务
    public function refreshPlayerData()
    {
        $steamids = Steamuser::select('steamid')->get()->toArray();
        foreach($steamids as $item)
        {
            $steamid = $item['steamid'];
            $this->getPlayerRecentPlayedActual($steamid);
            $this->getBadgeListActual($steamid);
            Steamuser::setExpire($steamid);
        }
    }

    //  刷新用户的好友列表
    //  用于定时任务
    public function refreshPlayerFriendList()
    {
        $steamids = Steamuser::select('steamid')->get()->toArray();
        foreach($steamids as $item)
        {
            $steamid = $item['steamid'];
            $this->getFriendListActual($steamid);
        }
    }

    //  刷新用户的好友列表
    //  用于定时任务
    public function refreshPlayerGame()
    {
        $steamids = Steamuser::select('steamid')->get()->toArray();
        foreach($steamids as $item)
        {
            $steamid = $item['steamid'];
            $this->getPlayerOwnedGameActual($steamid);
        }
    }
}
