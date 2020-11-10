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

//laravel框架
Route::get('/', function () {
    return view('welcome');
});

//查看扩展
Route::get('/info',function (){
    phpinfo();
});

//微信开发者服务器接入(即支持get又支持post)
Route::match(['get','post'],'/wx','WxController@checkSignature');
//上传素材
Route::get('/guzzle2','WxController@guzzle2');
//获取access_token
Route::get('/access_token','WxController@access_token');
//天气(780)
Route::get('/weather1','WxController@weather1');
//自定义菜单
Route::get('/create_menu','WxController@create_menu');


//测试1
Route::get('/weather','WxController@weather');
//测试2
Route::get('/test','WxController@test');
//测试3(postman)
Route::get('test2','WxController@test2');//get
Route::post('test3','WxController@test3');//post(form-data)
Route::post('test4','WxController@test4');//post(raw)
//测试路由分组 test(prefix)
Route::prefix('/test')->group(function (){
    Route::get('/guzzle1','TestController@guzzle1');//使用guzzl发送get请求
    Route::get('/guzzle2','TestController@guzzle2');//上传素材
    Route::get('/weather','TestController@weather');//天气780
});

