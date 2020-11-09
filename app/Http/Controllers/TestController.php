<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
class TestController extends Controller
{
    //测试guzzle发送get请求
    public function guzzle1(){
        $url= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET')."";
        //使用guzzl发送get请求
        $client=new Client();//实例化客户端
        $response=$client->request('GET',$url,['verify'=>false]);//发起请求并接收响应
        $json_str=$response->getBody();//服务器的响应数据
        echo $json_str;
    }
    //测试上传素材
    public function guzzle2(){
        $access_token="";
        $type="image";
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=".$type." ";
        $client=new Client();//实例化客户端
        $response=$client->request('POST',$url,[
            'verify'=>false,
            'multipart'=>[
                [
                    'name'=>'media',
                    'contents'=>fopen('大海.jpg','r')
                ]   //上传的文件路径
            ]
        ]);  //发送请求并接收响应
        $data=$response->getBody();//服务器的响应数据
        echo $data;
    }
}
