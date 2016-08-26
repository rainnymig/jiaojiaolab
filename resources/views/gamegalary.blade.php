@extends('common')

@section('content')

<link href="{{ asset('/css/gamegalary-spec.css?ver=0036') }}" rel="stylesheet">
<script src="http://malsup.github.com/jquery.form.js"></script>
<script src="{{ asset('/echarts/echarts.min.js') }}"></script>
<script src="{{ asset('/echarts/theme/dark.js') }}"></script>
<script src="{{ asset('/js/gameGalarySpec.js?ver=0033.js') }}"></script>

<div class="container">
    <div style="margin: 15px;">
        <i class="fa fa-angle-double-left"></i>
        <a id="returnLink" href="http://rainnymiglab.cc/profile/{{ $steamid }}">返回个人主页</a>
    </div>
    <input id="playerSteamid"  type="hidden" content="{{ $steamid }}">
    <div class="gamegalary-body" id="gameGalary">
        <div class="col-lg-12 col-md-12 vertical-center-parent loading-container">
            <i class="fa fa-refresh fa-spin vertical-center-element"></i>
        </div>
    </div>
</div>

@endsection

