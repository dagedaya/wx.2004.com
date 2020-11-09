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
//微信开发者服务器接入(即支持get又支持post)
Route::match(['get','post'],'/wx','WxController@checkSignature');
//获取access_token
Route::get('/access_token','WxController@access_token');
//测试1
Route::get('/weather','WxController@weather');
//测试2
Route::get('/test','WxController@test');
//测试3(postman)
Route::get('test2','WxController@test2');//get
Route::post('test3','WxController@test3');//post(form-data)
Route::post('test4','WxController@test4');//post(raw)
Route::get('/openid','WxController@openid');

