<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
	<title>娇娇的蒸汽实验室</title>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="{{ asset('/css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/fonts.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/common.css?ver=2004') }}" rel="stylesheet">

	<script src="http://cdn.bootcss.com/jquery/2.1.3/jquery.min.js"></script>
	<script src="http://cdn.bootcss.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<!-- Fonts -->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body>
	<nav style="margin-bottom:0px;" class="navbar navbar-inverse steam-navbar">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand steam-navbar-brand" href="http://rainnymiglab.cc/homepage">娇娇的蒸汽实验室</a>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a class="steam-navbar-item" href="#">广场</a></li>
					<li><a class="steam-navbar-item" href="#">卡片</a></li>
				</ul>
                @if(!($value = Session::get('loginUser')))
                    <form action="{{ action('SteamHomeController@loginWithSteam') }}" method="get" class="navbar-form navbar-right" role="form">
                        <input class="steam-login-btn" type="image" src="http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_large_noborder.png">
                        <input type="hidden" name="login">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </form>
                @else
                    <ul class="nav navbar-nav navbar-right">
                        <li><img class="steam-navbar-avatar" src="{{ Session::get('loginUserAvatar') }}"></li>
                        <li><a style="padding: 0px;"  href="http://rainnymiglab.cc/profile/{{ Session::get('loginUserSteamId') }}"><p class="steam-navbar-username">{{ Session::get('loginUser') }}</p></a></li>
                        <li><a class="steam-navbar-control" href="{{ route('userLogout') }}">注销</a></li>
                    </ul>
                @endif
			</div>
		</div>
	</nav>

    @yield('content')

    <footer class="steam-footer">
        <div class="container">
            <div class="col-lg-4 col-md-4 col-lg-offset-4 col-md-offset-4">
                <p class="footer-info"><a href="http://www.miitbeian.gov.cn/" target="_blank">京 ICP 备 16010706 号</a></p>
            </div>
        </div>
    </footer>

</body>
</html>
