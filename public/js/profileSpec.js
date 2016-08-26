$(document).ready(function(){
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    //  上传背景相关操作
    $('#bgImageUploadForm').submit(function(){
        $(this).ajaxSubmit({
            type: "POST",
            dataType: "text",
            url: "http://rainnymiglab.cc/fileupload/backgroundimageupload/"+$('#playerMainSteamid').text(),
            success: parsePlayerBackground,
            error: parseError
        });
        return false;
    });
    function parsePlayerBackground(bgImage)
    {
        var alertDisplay = $('#fileUploadAlert');
        if(bgImage == 'wrongfiletype')
        {
            alertDisplay.text('请选择图片类型的文件：jpg, jpeg, png, bmp 或 gif');
            alertDisplay.slideDown();
        }
        else if(bgImage == 'uploadfailure')
        {
            alertDisplay.text('上传失败，请稍后再试。');
            alertDisplay.slideDown();
        }
        else if(bgImage == 'nofileselect')
        {
            alertDisplay.text('请选择要上传的图片。');
            alertDisplay.slideDown();
        }
        else
        {
            alertDisplay.slideUp();
            $('#imagePreviewWindow').html('<img src="'+bgImage+'" class="image-preview">');
            $('#currentImage').val(bgImage);
        }
    }
    $('#btnBgImageConfirm').click(function(){
        var imageurl;
        if(($('#useSteamBgCheck').prop('checked')))
        {
            imageurl = "useSteamBg";
        }
        else if('' != $('#currentImage').val())
        {
            imageurl = $('#currentImage').val();
        }
        else
        {
            $('#modalBackgroundUpload').modal('hide');
            return;
        }
        $.ajax({
            type: "POST",
            dataType: 'text',
            url: "http://rainnymiglab.cc/fileupload/backgroundimageset/"+$('#playerMainSteamid').text(),
            data: {
                imageUrl: imageurl,
                _token: $('meta[name="_token"]').attr('content')
            },
            success: confirmBgImage,
            error: parseError
        });
        $('#modalBackgroundUpload').modal('hide');
    });
    function confirmBgImage(bgImage)
    {
        if(bgImage != ''){
            var pageBody = $('body');
            pageBody.css('background-image', 'url\(\''+bgImage+'\'\)');
        }
    }

    function parseError()
    {
        alert('fail');
    }

    //  获取玩家的随机几款游戏用作展示
    var randomGameCount = 9;
    $.ajax({
        type: "GET",
        dataType: "JSON",
        url: "http://rainnymiglab.cc/getProfileInfo/getplayerrandomgame/"+$('#playerMainSteamid').text()+"/"+randomGameCount,
        success: parseRandomGame,
        error: parseError
    });
    function parseRandomGame(randomGameJson)
    {
        var gameSection = $('#gameDisplaySection');
        gameSection.html('');
        if(randomGameJson != 0)
        {
            gameSection.append('<div class="clearfix" style="width: 100%;"><ul class="rgSlider"></ul></div>');
            var rgSlider = $('.rgSlider');
            var smallImageUrl = "";
            var bigImageUrl = "";
            for(var gIndex in randomGameJson)
            {
                smallImageUrl = 'http://media.steampowered.com/steamcommunity/public/images/apps/'+randomGameJson[gIndex].appid+'/'+randomGameJson[gIndex].gamelogo+'.jpg';
                bigImageUrl = 'http://cdn.steamstatic.com.8686c.com/steam/apps/'+randomGameJson[gIndex].appid+'/capsule_616x353.jpg';
                rgSlider.append('<li><img src="'+smallImageUrl+'" data-large-src="'+bigImageUrl+'"></li>');
            }
            rgSlider.pgwSlider({
                selectionMode: 'mouseOver',
                transitionEffect: 'fading',
                autoSlide: true
            });
        }
        else
        {

        }
        $('img').on('error', function(){
            $(this).attr('src', 'http://rainnymiglab.cc/image/imgError/imgErrorDefault616x353.png');
        });
    }


    //  获取玩家好友列表
    $.ajax({
        type: "GET",
        dataType: "JSON",
        url: "http://rainnymiglab.cc/getProfileInfo/getfriendlist/"+$('#playerMainSteamid').text()+"/normal",
        success: parseFriendJson,
        error: parseError
    });
    function parseFriendJson(friendJson)
    {
        var friendSection = $('#friendDisplaySection');
        friendSection.html('');
        var i = 0;
        var limit = 5;
        var rowId;
        var currentRow;
        if(friendJson.friendAvatar.length-1 < 5)
        {
            limit = friendJson.friendAvatar.length-1;
        }
        for(i = 0; i <= limit; i++)
        {
            if(i==0 || i==2 || i==4)
            {
                rowId = 'row'+i;
                friendSection.append('<div class="row" id="'+rowId+'"></div>');
                currentRow = $('#friendDisplaySection #'+rowId);
                currentRow.append('<div class="col-md-5 col-sm-5 friend-info-card"><img src="'+friendJson.friendAvatar[i]+'" class="friend-avatar"></div>');
            }
            else if(i==1 || i==3 || i==5)
            {
                currentRow.append('<div class="col-md-5 col-sm-5 col-md-offset-1 col-sm-offset-1 friend-info-card"><img src="'+friendJson.friendAvatar[i]+'" class="friend-avatar"></div>');
            }
        }
    }

    //  获取玩家个人主页背景图片
    $.ajax({
        type: "GET",
        dataType: "text",
        url: "http://rainnymiglab.cc/getProfileInfo/getbackgroundimage/"+$('#playerMainSteamid').text()+"/normal",
        success: parseBackgroundImage,
        error: parseError
    });
    function parseBackgroundImage(bgImage)
    {
        if(bgImage != ''){
            var pageBody = $('body');
            pageBody.css('background-image', 'url\(\''+bgImage+'\'\)');
        }
    }

    //  获取玩家的玩家卡
    $.ajax({
        type: "GET",
        dataType: "text",
        url: "http://rainnymiglab.cc/playercard/"+$('#playerMainSteamid').text(),
        success: parsePlayerCard,
        error: parseError
    });
    function parsePlayerCard(playerCardImage)
    {
        var cardSection = $('#badgeDisplaySection');
        cardSection.html('');
        if(playerCardImage != ''){
            var cardImage = '<img class="full-image" src ="'+playerCardImage+'?'+Math.random()+'">';
            cardSection.append(cardImage);
        }
        else
        {

        }
        
        //  获取玩家的统计数据 
        //  特意等到获取完玩家卡之后再请求数据
        //  因为用户第一次登录的时候数据库是空的
        //  请求统计数据就会出问题
        //  所以先获取了玩家卡，保证玩家数据都已经读取了
        //  再请求统计数据
        $.ajax({
            type: "GET",
            dataType: "JSON",
            url: "http://rainnymiglab.cc/getProfileInfo/getplayerstat/"+$('#playerMainSteamid').text()+"/normal",
            success: parsePlayerStat,
            error: parseError
        });
    }

    //  历史总游戏时长图的基本设置
    var ptfChart = echarts.init(document.getElementById('playtimeForeverChart'));
    ptfChart.setOption({
        title: {
            show: true,
            text: '历史总游戏时长',
            textStyle: {
                color: '#bbbbbb'
            },
            left: 'center',
            subtext: '(分钟)'
        },
        tooltip: {
            show: true,
            trigger: 'item',
            formatter: "{b}<br>{c} min ({d}%)"
        },
        toolbox: {
            show: true,
            feature: {
                saveAsImage: {show: true}
            }
        },
        series: [
            {
                name: '历史总游戏时长分布',
                type: 'pie',
                radius: '70%',
                label: {
                    normal: {
                        show: false
                    },
                    emphasis: {
                        show: false
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    },
                    emphasis: {
                        show: false
                    }
                },
                roseType: 'radius'
            }
        ],
        backgroundColor: 'rgba(40, 40, 40, 0.3)'
    });

    //  近两周游戏时长图的基本设置
    var pt2wChart = echarts.init(document.getElementById('playtime2WeeksChart'));
    pt2wChart.setOption({
        title: {
            show: true,
            text: '近两周游戏时长',
            textStyle: {
                color: '#bbbbbb'
            },
            left: 'center',
            subtext: '(分钟)'
        },
        tooltip: {
            show: true,
            trigger: 'item',
            formatter: "{b}<br>{c} min ({d}%)"
        },
        toolbox: {
            show: true,
            feature: {
                saveAsImage: {show: true}
            }
        },
        series: [
            {
                name: '近两周游戏时长分布',
                type: 'pie',
                radius: '70%',
                label: {
                    normal: {
                        show: false
                    },
                    emphasis: {
                        show: false
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    },
                    emphasis: {
                        show: false
                    }
                },
                roseType: 'radius'
            }
        ],
        backgroundColor: 'rgba(40, 40, 40, 0.3)'
    });
    function parsePlayerStat(playerStat)
    {
        //alert('ddddd');
        var statSection = $('#playerStatSection');


        //  画历史游戏总时间分布图
        if(playerStat.statDetailGtForever != 0 && playerStat.statDetailGtForever[0].playtimeforever != 0)
        {
            var gtForeverArray = new Array();
            var gtForeverLegendArray = new Array();
            for(var gtForever in playerStat.statDetailGtForever)
            {
                //  为了使用 echarts 绘图，要变成 name-value 对
                gtForeverArray.push({
                    name: playerStat.statDetailGtForever[gtForever].gamename,
                    value: playerStat.statDetailGtForever[gtForever].playtimeforever
                });
                gtForeverLegendArray.push(playerStat.statDetailGtForever[gtForever].gamename);
            }
            ptfChart.setOption({
                legend: {
                    data: gtForeverLegendArray,
                    top: 'bottom',
                    textStyle: {
                        color: '#bbbbbb'
                    }
                },
                series: [{
                    data: gtForeverArray
                }]
            });
        }
        else
        {
            ptfChart.setOption({
                title: {
                    subtext: '你没有玩过 steam 的游戏'
                }
            });
        }

        //  画近两周游戏时间分布图
        if(playerStat.statDetailGt2Weeks != 0 && playerStat.statDetailGt2Weeks[0].playtime2weeks != 0)
        {
            var gt2WeeksArray = new Array();
            var gt2WeeksLegendArray = new Array();
            for(var gt2Weeks in playerStat.statDetailGt2Weeks)
            {
                //  为了使用 echarts 绘图，要变成 name-value 对
                gt2WeeksArray.push({
                    name: playerStat.statDetailGt2Weeks[gt2Weeks].gamename,
                    value: playerStat.statDetailGt2Weeks[gt2Weeks].playtime2weeks
                });
                gt2WeeksLegendArray.push(playerStat.statDetailGt2Weeks[gt2Weeks].gamename);
            }
            pt2wChart.setOption({
                legend: {
                    data: gt2WeeksLegendArray,
                    top: 'bottom',
                    textStyle: {
                        color: '#bbbbbb'
                    }
                },
                series: [{
                    data: gt2WeeksArray
                }]
            });
        }
        else
        {
            pt2wChart.setOption({
                title: {
                    subtext: '你近两周没有玩过 steam 的游戏'
                }
            });
        }
    }

});
