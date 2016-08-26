<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Steamuser;
use App\Http\Controllers\SteamDataController;
use Image;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlayerCardController extends Controller {

    protected $steamData;
    

    public function __construct(SteamDataController $steamData)
    {
        $this->steamData = $steamData;
    }
	//
    public function makePlayerCard($steamid)
    {
        $steamRecentPlayed = $this->steamData->getPlayerRecentPlayed($steamid, 'normal');
        $steamRecentPlayed = json_decode($steamRecentPlayed, true);
        $steamBadge = $this->steamData->getBadgeList($steamid, 'normal');
        $steamBadge = json_decode($steamBadge, true);
        $steamBackground = $this->steamData->getBackgroundImage($steamid);
        $steamBasicInfo = $this->steamData->getBasicInfo($steamid);
        Steamuser::setExpire($steamid);

        /*
        echo '<pre>';
        print_r($steamRecentPlayed);
        print_r($steamBadge);
        echo '</pre>';
         */

        //  创建玩家卡图像对象
        $cardIm = Image::canvas(640, 200, '#ccc');

        //  背景图
        if($steamBackground != '')
        {
            $bgIm = Image::make($steamBackground)->fit(640, 200);
            $bgIm->blur(15);
            $cardIm->insert($bgIm);
            $bgIm->destroy();
        }

        //  各部分背景方框
        $cardIm->rectangle(200, 55, 335, 180, function($draw){
            $draw->background('rgba(160, 160, 160, 0.6)');
        });
        $cardIm->rectangle(350, 55, 630, 180, function($draw){
            $draw->background('rgba(160, 160, 160, 0.6)');
        });

        //  最近玩的游戏
        if($steamRecentPlayed['recentplayedimage1'] != '')
        {
            $re1Im = Image::make($steamRecentPlayed['recentplayedimage1'])->heighten(45);
            $cardIm->insert($re1Im, 'top-left', 360, 65);
            $re1Im->destroy();

        }
        if($steamRecentPlayed['recentplayedimage2'] != '')
        {
            $re2Im = Image::make($steamRecentPlayed['recentplayedimage2'])->heighten(45);
            $cardIm->insert($re2Im, 'top-left', 500, 65);
            $re2Im->destroy();

        }
        if($steamRecentPlayed['recentplayedimage3'] != '')
        {
            $re3Im = Image::make($steamRecentPlayed['recentplayedimage3'])->heighten(45);
            $cardIm->insert($re3Im, 'top-left', 360, 125);
            $re3Im->destroy();

        }

        $cardIm->text('最近玩的游戏', 360, 45, function($font){
            $font->file(public_path('font/微软雅黑.ttf'));
            $font->size(24);
            $font->color('#222222');
        });

        //  徽章
        if($steamBadge['badgeimage1'] != '')
        {
            $bg1Im = Image::make($steamBadge['badgeimage1'])->heighten(54);
            $cardIm->insert($bg1Im, 'top-left', 210, 65);
            $bg1Im->destroy();

        }
        if($steamBadge['badgeimage2'] != '')
        {
            $bg2Im = Image::make($steamBadge['badgeimage2'])->heighten(54);
            $cardIm->insert($bg2Im, 'top-left', 270, 65);
            $bg2Im->destroy();

        }
        if($steamBadge['badgeimage3'] != '')
        {
            $bg3Im = Image::make($steamBadge['badgeimage3'])->heighten(54);
            $cardIm->insert($bg3Im, 'top-left', 210, 125);
            $bg3Im->destroy();

        }
        if($steamBadge['badgeimage4'] != '')
        {
            $bg4Im = Image::make($steamBadge['badgeimage4'])->heighten(54);
            $cardIm->insert($bg4Im, 'top-left', 270, 125);
            $bg4Im->destroy();

        }
        $cardIm->text('我的徽章', 200, 45, function($font){
            $font->file(public_path('font/微软雅黑.ttf'));
            $font->size(24);
            $font->color('#222222');
        });
        
        //  玩家头像
        $avaIm = Image::make($steamBasicInfo['avatarImage'])->heighten(120);
        $cardIm->insert($avaIm, 'left', 20, 0);
        $avaIm->destroy();


        $path = public_path('userContent/playerCard/'.$steamid.'PlayerCard.png');
        $cardIm->encode('png')->save($path);
        $webPath = config('app.url').'/'.'userContent/playerCard/'.$steamid.'PlayerCard.png';
        $user = Steamuser::where('steamid', $steamid)->first();
        $user->playercardimage = $webPath;
        $user->save();
        //return $cardIm->response('png');
        /*
        echo '<pre>';
        print_r($steamRecentPlayed);
        print_r($steamBadge);
        print_r($steamBackground);
        print_r($steamBasicInfo);
        echo '</pre>';
         */
    }

    //  刷新指定用户的玩家卡
    //  用于定时任务
    public function refreshPlayerCard()
    {
        $steamids = Steamuser::select('steamid')->get()->toArray();
        foreach($steamids as $item)
        {
            $steamid = $item['steamid'];
            $this->makePlayerCard($steamid);
        }
    }

    public function getPlayerCard($steamid)    
    {
        $queryResult = Steamuser::select('playercardimage')->where('steamid', $steamid)->first()->toArray();
        $playerCardImage = $queryResult['playercardimage'];
        if($playerCardImage == '')
        {
            $this->makePlayerCard($steamid);
            $queryResult = Steamuser::select('playercardimage')->where('steamid', $steamid)->first()->toArray();
            $playerCardImage = $queryResult['playercardimage'];
        }
        return $playerCardImage;
    }
}
