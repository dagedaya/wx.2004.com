<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//查看扩展
Route::get('/info',function (){
    phpinfo();
});
//微信开发者服务器接入
Route::match(['get','post'],'/wx','WxController@checkSignature');
//获取access_token
Route::get('/access_token','WxController@access_token');

