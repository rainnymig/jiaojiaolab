@extends('common')

@section('content')

<link href="{{ asset('/css/profile-spec.css?ver=0612') }}" rel="stylesheet">
<link href="{{ asset('/pgwslider/pgwslider.min.css') }}" rel="stylesheet" type="text/css">
<script src="http://malsup.github.com/jquery.form.js"></script>
<script src="{{ asset('/echarts/echarts.min.js') }}"></script>
<script src="{{ asset('/echarts/theme/dark.js') }}"></script>
<script src="{{ asset('js/profileSpec.js?ver=0967') }}"></script>
<script src="{{ asset('pgwslider/pgwslider.min.js') }}"></script>


<div class="row" id="mainTitleDiv">
    <div class="col-lg-3 col-md-3">
    </div>
    <div class="col-lg-6 col-md-6 text-center">
        <div class="col-md-4 col-sm-4 player-info-frame vertical-center-parent">
            <div class="vertical-center-element">
                <p id="playerMainName">{{ $steamUserInfo['personaname'] }}</p>
                <p id="playerMainSteamid">{{ $steamUserInfo['steamid'] }}</p>
                <p id="playerMainState">
                    @if($steamUserInfo['personastate']==0)
                        离线
                    @elseif(isset($steamUserInfo['gameid']))
                        游戏中
                    @elseif($steamUserInfo['personastate']==1)
                        在线
                    @elseif($steamUserInfo['personastate']==2)
                        忙碌
                    @elseif($steamUserInfo['personastate']==3)
                        离开
                    @elseif($steamUserInfo['personastate']==4)
                        打盹
                    @elseif($steamUserInfo['personastate']==5)
                        渴望交易
                    @elseif($steamUserInfo['personastate']==6)
                        渴望游戏
                    @endif
                </p>
            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <img class="player-main-avatar" id="playerMainAvatar" src="{{ $steamUserInfo['avatarfull'] }}" 
            @if($steamUserInfo['personastate']==0)
                style="box-shadow: 0px 0px 25px rgba(106,106,106,1);"
            @elseif(isset($steamUserInfo['gameid']))
                style="box-shadow: 0px 0px 25px rgba(143,185,59,1)"
            @else
                style="box-shadow: 0px 0px 25px rgba(83,164,196,1);"
            @endif
            >
        </div>
        <div class="col-md-4 col-sm-4 player-info-frame vertical-center-parent">
            <div class="vertical-center-element">
                <p id="playerLastLogoff">上次在线：</p>
                <p>{{ date("H:i Y-n-j l", $steamUserInfo['lastlogoff']) }}</p>
                <p>玩家级数：{{ $steamUserBadge['player_level'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="container" id="mainContentContainer">
    <div class="row">
        <div class="display-section display-section-8">
            <div class="display-section-title">
                <i class="fa fa-user"></i>
                玩家卡
            </div>
            <div class="display-section-body" id="badgeDisplaySection">
                <div class="col-lg-12 col-md-12 vertical-center-parent loading-container">
                    <i class="fa fa-refresh fa-spin vertical-center-element"></i>
                </div>
            </div>
        </div>
        <div class="display-section display-section-4">
            <div class="display-section-title">
                <i class="fa fa-group"></i>
                我的好友
            </div>
            <div class="display-section-body" id="friendDisplaySection">
                <div class="col-lg-12 col-md-12 vertical-center-parent loading-container">
                    <i class="fa fa-refresh fa-spin vertical-center-element"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="tiny-dummy"></div>
    <div class="row">
        <div class="display-section display-section-12">
            <div class="display-section-title">
                <i class="fa fa-pie-chart"></i>
                我的统计数据
            </div>
            <div class="display-section-body" id="playerStatSection">
                <div class="clearfix">
                    <div id="playtimeForeverChart" style="height: 400px; width: 50%; float: left;"></div>
                    <div id="playtime2WeeksChart" style="height: 400px; width: 50%; float: left;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tiny-dummy"></div>
    <div class="row">
        <div class="display-section display-section-12">
            <div class="display-section-title">
                <i class="fa fa-steam"></i>
                我拥有的游戏
            <a class="display-section-subtitle" href="http://rainnymiglab.cc/gamegalary/{{ $steamUserInfo['steamid'] }}">查看全部</a>
            </div>
            <div class="display-section-body" id="gameDisplaySection">
                <div class="col-lg-12 col-md-12 vertical-center-parent loading-container">
                    <i class="fa fa-refresh fa-spin vertical-center-element"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<button class="btn btn-primary btn-change-bg" data-toggle="modal" data-target="#modalBackgroundUpload">编辑背景</button>

<div class="modal fade" id="modalBackgroundUpload" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog steam-modal">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 id="uploadModalLabel" class="modal-title">编辑背景</h4>
            </div>
            <div class="modal-body">
                <div id="imagePreviewWindow" class="text-center vertical-center-parent">
                    <p class="imagePreviewHolder vertical-center-element">图片预览</p>
                </div>
                <form role="form" action="" method="post" id="bgImageUploadForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="bgImageFileUpload">从本地选择图片上传</label>
                        <input class="form-control" type="file" name="bgImageFileUpload" id="bgImageFileUpload">
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="useSteamBgCheck" value="useSteamBg" style="vertical-align: text-top;"  id="useSteamBgCheck">&nbsp使用 Steam 背景
                    </div>
                    <button type="submit" class="btn btn-success" id="btnBgImageUpload">上传</button>
                    <div class="alert alert-danger" id="fileUploadAlert" style="display: none;"></div>
                    <input type="hidden" value="" name="currentImage" id="currentImage">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="btnBgImageConfirm">确定</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

@endsection

