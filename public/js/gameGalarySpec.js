$(document).ready(function(){
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    var playerSteamid = $('#playerSteamid').attr('content');
    var gameSection = $('#gameGalary');
    var gameCount = 0;

    //  游戏分页显示
    //  初始显示第 0 页（即第 1 页）
    //  每页显示 15 个游戏
    var gamePage = 0;
    var gamePageDisplayCount = 15;

    function parseError()
    {
        alert('fail');
    }

    //  获取玩家游戏列表    
    $.ajax({
        type: "GET",
        dataType: "JSON",
        url: "http://rainnymiglab.cc/getProfileInfo/getplayerownedgame/"+playerSteamid+"/normal/"+gamePage+'/'+gamePageDisplayCount,
        success: parsePlayerGameJson,
        error: parseError
    });


    function parsePlayerGameJson(gameJson)
    {
        gameSection.html('');
        var gameRow;
        var gameImage;
        var gameDescribe;
        var gameShopUrl;
        var gameSteamDBUrl;
        gameCount = gameJson.gameCount;
        $.each(gameJson.gameList, function(i, item){
            if(0 == i%3)
            {
                gameSection.append('<div class="row"></div>');
                gameRow = gameSection.children('div.row').last();
            }
            gameImage = '<img class="full-image hover-blur" src="https://steamcdn-a.akamaihd.net/steam/apps/'+item.appid+'/header.jpg">';            
            gameSteamDBUrl = 'https://steamdb.info/app/'+item.appid+'/';
            gameShopUrl = 'http://store.steampowered.com/app/'+item.appid+'/';
            gameDescribe = '<div style="" class="game-describe-container"><div class="game-description-title">'+item.gamename+'</div><hr><div class="game-description-body clearfix"><a target="_blank" href="'+gameShopUrl+'" class="pull-left">商店页面</a><a target="_blank" href="'+gameSteamDBUrl+'" class="pull-right">Steam DB</a></div></div>';
            gameRow.append('<div style="position:relative;" class="col-lg-4 col-md-4 col-sm-4"><div class="game-slot">'+gameDescribe+gameImage+'</div></div>');
        });

        if(gamePage == 0)
        {
            var navBtn = '<div class="clearfix"><div class="nav-btn-container pull-right"><a id="nextGamePage">下一页 <i class="fa fa-arrow-right"></i></a></div></div>';
            gameSection.append(navBtn);
        }
        else if(((gamePage+1)*gamePageDisplayCount) < gameCount)
        {
            var navBtn = '<div class="clearfix"><div class="nav-btn-container pull-left"><a id="prevGamePage"> <i class="fa fa-arrow-left"></i>上一页</a></div><div class="nav-btn-container pull-right"><a id="nextGamePage">下一页 <i class="fa fa-arrow-right"></i></a></div></div>';
            gameSection.append(navBtn);
        }
        else
        {
            var navBtn = '<div class="clearfix"><div class="nav-btn-container pull-left"><a id="prevGamePage"> <i class="fa fa-arrow-left"></i>上一页</a></div></div>';
            gameSection.append(navBtn);
        }

        //  若图片加载出错则替换为默认图片
        $('img').on('error', function(){
            $(this).attr('src', 'http://rainnymiglab.cc/image/imgError/imgErrorDefault460x215.png');
        });

        //  读取前一页列表
        $('#prevGamePage').click(function(){
            if(gamePage > 0)
            {
                gameSection.html('');
                gameSection.append('<div class="col-lg-12 col-md-12 vertical-center-parent loading-container"><i class="fa fa-refresh fa-spin vertical-center-element"></i></div>');
                gamePage--;
                $.ajax({
                    type: "GET",
                    dataType: "JSON",
                    url: "http://rainnymiglab.cc/getProfileInfo/getplayerownedgame/"+playerSteamid+"/normal/"+gamePage+'/'+gamePageDisplayCount,
                    success: parsePlayerGameJson,
                    error: parseError
                });
            }
        });

        //  读取后一页列表
        $('#nextGamePage').click(function(){
            if((gamePage+1)*gamePageDisplayCount < gameCount)
            {
                gameSection.html('');
                gameSection.append('<div class="col-lg-12 col-md-12 vertical-center-parent loading-container"><i class="fa fa-refresh fa-spin vertical-center-element"></i></div>');
                gamePage++;
                $.ajax({
                    type: "GET",
                    dataType: "JSON",
                    url: "http://rainnymiglab.cc/getProfileInfo/getplayerownedgame/"+playerSteamid+"/normal/"+gamePage+'/'+gamePageDisplayCount,
                    success: parsePlayerGameJson,
                    error: parseError
                });
            }
        });
    }


});
