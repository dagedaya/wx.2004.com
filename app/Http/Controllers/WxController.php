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
            //1.接收数据
            $xml_str = file_get_contents('php://input');
            //记录日志
//            file_put_contents('wx_event.log',$xml_str,'FILE_APPEND');
//            echo "$echostr";
//            die;
            //2.把xml文本转换成php的数组或者对象
            $data = simplexml_load_string($xml_str, 'SimpleXMLElement', LIBXML_NOCDATA);
            //判断该数据包是否是订阅的事件推送
            if (strtolower($data->MsgType) == "event") {
                //关注
                if (strtolower($data->Event == 'subscribe')) {
                    //回复用户消息(纯文本格式)
                    $toUser = $data->FromUserName;
                    $fromUser = $data->ToUserName;
                    $msgType = 'text';
                    $content = '欢迎关注微信公众账号';
                    $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                    $info = sprintf($template, $toUser, $fromUser, time(), $msgType, $content);
                    return $info;
                }
                if (strtolower($data->Event == 'unsubscribe')) {
                   //清除用户的信息
                }
                if(strtolower($data->MsgType) == "text"){
//                   file_put_contents('wx_text.log',$data,'FILE_APPEND');
//                    echo "";
//                    die;
                    switch ($data->Content){
                        case "天气":
                            $category=1;
                            $key='4e268e1bc28d4d2a9223e11a55b9dab5';
                            $url="https://devapi.qweather.com/v7/weather/now?location=101010100&key=".$key."&gizp=n";
                            $api=file_put_contents($url);
                            $api=json_decode($api,true);
                            $content = "天气状态：".$api['now']['text'].'
                                风向：'.$api['now']['windDir'];
                            break;
                    }
                    $toUser   = $data->FromUserName;
                    $fromUser = $data->ToUserName;
                    if($category==1){
                        $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                        $info = sprintf($template, $toUser, $fromUser, time(),'text', $content);
                        return $info;
                    }
                }
            } else {
                return false;
            }
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
