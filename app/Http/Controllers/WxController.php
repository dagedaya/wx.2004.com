<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WxController extends Controller
{
    //微信接入
    public function checkSignature(Request $request)
    {
        $echostr = $request->echostr;
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = env('WX_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            echo $echostr;
        } else {
            return false;
        }
    }
    //获取access_token并缓存
    public function access_token(){
        $key="access_token:";
        //判断是否有缓存
        $token=Redis::get($key);
        if($token){
            echo "有缓存";
        }else{
            echo "无缓存";
            $url= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET')."";
            $response=file_get_contents($url);
            $data=json_decode($response,true);
            $token=$data['access_token'];
            //存到redis中
            Redis::set($key,$token);
            //设置过期时间
            Redis::expire($key,3600);
        }
        echo "access_token".$token;
    }
}
