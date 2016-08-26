<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as'=>'getHomepage', 'uses'=>'SteamHomeController@showHomepage']);

Route::get('home', 'HomeController@index');



//  主页及登录
Route::get('homepage', ['as'=>'getHomepage', 'uses'=>'SteamHomeController@showHomepage']);

Route::get('logout', ['as'=>'userLogout', 'uses'=>'SteamHomeController@userLogout']);

Route::get('loginwithsteam', 'SteamHomeController@loginWithSteam');



//  测试
Route::get('dbt', 'SteamDataController@dbTest');

Route::get('getlocaldata/{steamid}', 'SteamDataController@getBackgroundImage');

Route::get('localdatatest', 'SteamDataController@localDataTest');


//  获取玩家资料
Route::get('getProfileInfo/getplayerownedgame/{steamid}/{refresh}/{gamepage}/{displaycount}', 'SteamDataController@getPlayerOwnedGame');

Route::get('getProfileInfo/getplayerrandomgame/{steamid}/{requireCount}', 'SteamDataController@getPlayerRandomGame');

Route::get('getProfileInfo/getbackgroundimage/{steamid}/{refresh}', 'SteamDataController@getBackgroundImage');

Route::get('getProfileInfo/getfriendlist/{steamid}/{refresh}', 'SteamDataController@getFriendList');

Route::get('getProfileInfo/getplayerrecentplayed/{steamid}/{refresh}', 'SteamDataController@getPlayerRecentPlayed');

Route::get('getProfileInfo/getbadgelist/{steamid}/{refresh}', 'SteamDataController@getBadgeList');

Route::get('getProfileInfo/getplayerstat/{steamid}/{refresh}', 'SteamDataController@getPlayerStat');

Route::get('profile/{steamid}', ['as'=>'profile', 'uses'=>'SteamDataController@showPlayerProfile']);

//  显示玩家的游戏目录的页面
Route::get('gamegalary/{steamid}', 'SteamGameController@showPlayerGameGalary');

//  文件上传
Route::post('fileupload/backgroundimageupload/{steamid}', 'FileUploadController@uploadBgImage');

Route::post('fileupload/backgroundimageset/{steamid}', 'FileUploadController@setBgImage');

//  玩家卡
Route::get('playercard/{steamid}', 'PlayerCardController@getPlayerCard');

Route::get('remakeplayercard/{steamid}', 'PlayerCardController@makePlayerCard');
