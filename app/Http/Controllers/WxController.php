<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Model\WxUserModel;
class WxController extends Controller
{
    //测试
    public function test(){
        $toUser="abc";
        $token=$this->access_token();
//        echo $token;die;
      $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$toUser."&lang=zh_CN";
        echo $url;
    }
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
                    $content = '欢迎关注了我';
                    //根据OPENID获取用户信息（并且入库）
                        //1.获取openid
                    $token=$this->access_token();
                    $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$toUser."&lang=zh_CN";
                    file_put_contents('user_access.log',$url);
                    $user=file_get_contents($url);
                    $users=json_decode($user,true);
                    file_put_contents('user_access.log1',$users.'\r\n',FILE_APPEND );
//                    die;
                    $data=[
                        'subscribe'=>$users['subscribe'],
                        'openid'=>$users['openid'],
                        'nickname'=>$users['nickname'],
                        'sex'=>$users['sex'],
                        'city'=>$users['city'],
                        'country'=>$users['country'],
                        'province'=>$users['province'],
                        'language'=>$users['language'],
                    ];
                    UserInfoModel::insert($data);
                    //%s代表字符串(发送信息)
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
                //取关
                if (strtolower($data->Event == 'unsubscribe')) {
                    //清除用户的信息
                }
            }
            if(strtolower($data->MsgType) == "text"){
//                   file_put_contents('wx_text.log',$data,'FILE_APPEND');
//                    echo "";
//                    die;
                    switch ($data->Content){
                        case "天气":
                            $category=1;
                            $key='4e268e1bc28d4d2a9223e11a55b9dab5';
                            $url="https://devapi.qweather.com/v7/weather/now?location=101010100&key=".$key."&gzip=n";
                            $api=file_get_contents($url);
                            $api=json_decode($api,true);
                            $content = "天气状态：".$api['now']['text'].'
                                风向：'.$api['now']['windDir'];
                            break;
                         case "时间";
                            $category=1;
                            $content=date('Y-m-d H:i:s',time());
                            break;
                        default:
                            $category = 1;
                            $content  = "啊，亲，我疯了，你在说什么";
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
                        $info = sprintf($template, $toUser, $fromUser, time(),'text',$content);
                        return $info;
                    }
                }
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
            //有缓存
//            echo $token;
        }else{
//            echo "无缓存";
            $url= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET')."";
            $response=file_get_contents($url);
            $data=json_decode($response,true);
            $token=$data['access_token'];
            //存到redis中
            Redis::set($key,$token);
            //设置过期时间
            Redis::expire($key,3600);
        }
        return $token;
    }
    //测试
    public function weather(){
        //天气
        $key='4e268e1bc28d4d2a9223e11a55b9dab5';
        $url="https://devapi.qweather.com/v7/weather/now?location=101010100&key=".$key."&gzip=n";
        $api=file_get_contents($url);
        $api=json_decode($api,true);
        $content = "天气状态：".$api['now']['text'].'
                                风向：'.$api['now']['windDir'];
//        echo $content;
        //openid
        $openid=$this->access_token();
        echo $openid;
    }
}
