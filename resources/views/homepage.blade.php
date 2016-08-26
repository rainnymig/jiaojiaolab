@extends('nonavcommon')

@section('content')

<!--
<div class="container">
    <div class="row text-center">
        <div class="col-lg-8 col-md-8 col-lg-offset-2 col-md-offset-2">
            <form action="{{ action('SteamHomeController@loginWithSteam') }}" method="get" class="form-horizontal" role="form">
                <input type="image" src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_large_noborder.png">
                <input type="hidden" name="login">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </form>
        </div>
    </div>
</div>
-->

<link href="{{ asset('/css/home-spec.css?ver=0002') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/superslide/dist/stylesheets/superslides.css') }}">
<script src="{{ asset('/superslide/javascripts/jquery.easing.1.3.js') }}"></script>
<script src="{{ asset('/superslide/javascripts/jquery.animate-enhanced.min.js') }}"></script>
<script src="{{ asset('/superslide/dist/jquery.superslides.min.js') }}" type="text/javascript" charset="utf-8"></script>

<div id="slides">
    <ul class="slides-container">
        <li>
            <img class="superslide-image"  src="{{ asset('/image/steam-wallpaper-11.jpg') }}">
            <div class="superslide-description-container" id="sdcontainer1">
                <h3>娇娇的蒸汽实验室</h3>
                <h1>与世界分享</h1>
                <h1>你与 steam 的经历</h1>
                @if(!($value = Session::get('loginUser')))
                    <form action="{{ action('SteamHomeController@loginWithSteam') }}" method="get" class="form-horizontal" role="form">
                        <input type="image" src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_02.png">
                        <input type="hidden" name="login">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                @else
                    <a href="http://rainnymiglab.cc/profile/{{ Session::get('loginUserSteamId') }}" class="btn btn-success btn-lg">前往我的主页</a>
                @endif
            </div>
        </li>
        <li>
            <img class="superslide-image" src="{{ asset('/image/steam-12321.png') }}">
            <div class="superslide-description-container" id="sdcontainer2">
                <h3>娇娇的蒸汽实验室</h3>
                <h1>一键生成</h1>
                <h1>专属于你的 steam 玩家卡片</h1>
                <h5>可用于你自己的网站或者论坛签名</h5>
                @if(!($value = Session::get('loginUser')))
                    <form action="{{ action('SteamHomeController@loginWithSteam') }}" method="get" class="form-horizontal" role="form">
                        <input type="image" src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_02.png">
                        <input type="hidden" name="login">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                @else
                    <a href="http://rainnymiglab.cc/profile/{{ Session::get('loginUserSteamId') }}" class="btn btn-success btn-lg">前往我的主页</a>
                @endif
            </div>
        </li>
        <li>
            <img class="superslide-image" src="{{ asset('/image/Gamers.jpg') }}">
            <div class="superslide-description-container" id="sdcontainer3">
                <h3>娇娇的蒸汽实验室</h3>
                <h1>寻找</h1>
                <h1>兴趣相投的玩家</h1>
                @if(!($value = Session::get('loginUser')))
                    <form action="{{ action('SteamHomeController@loginWithSteam') }}" method="get" class="form-horizontal" role="form">
                        <input type="image" src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_02.png">
                        <input type="hidden" name="login">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                @else
                    <a href="http://rainnymiglab.cc/profile/{{ Session::get('loginUserSteamId') }}" class="btn btn-success btn-lg">前往我的主页</a>
                @endif
            </div>
        </li>
        <li>
            <img class="superslide-image" src="{{ asset('/image/maxresdefault1.png') }}">
            <div class="superslide-description-container" id="sdcontainer4">
                <h3>娇娇的蒸汽实验室</h3>
                <h1>享受</h1>
                <h1>你的游戏生活</h1>
                @if(!($value = Session::get('loginUser')))
                    <form action="{{ action('SteamHomeController@loginWithSteam') }}" method="get" class="form-horizontal" role="form">
                        <input type="image" src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_02.png">
                        <input type="hidden" name="login">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                @else
                    <a href="http://rainnymiglab.cc/profile/{{ Session::get('loginUserSteamId') }}" class="btn btn-success btn-lg">前往我的主页</a>
                @endif
            </div>
        </li>
    </ul>
    <nav class="slides-navigation">
        <a href="#" class="next">
            <i style="font-size: 24px;" class="fa fa-chevron-right"></i>
        </a>
        <a href="#" class="prev">
            <i style="font-size: 24px;" class="fa fa-chevron-left"></i>
        </a>
    </nav>
</div>

<script>
$(function(){
    $('#slides').superslides({
        animation: 'slide' 
    });
});
</script>

@endsection
