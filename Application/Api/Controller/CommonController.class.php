<?php

/**
 * 接口公共控制器
 */
namespace Api\Controller;
use Think\Controller;
use Common\Service;


class CommonController extends Controller{


    public function __construct()
    {

    }



    /**
     * 返回错误信息
     *
     * @access protected
     * @param str $message 要返回的错误信息
     * @param int $code    错误代号
     * @param int $type    错误类型
     * @return void
     */
    protected function error($message = '未知错误', $code = 1, $type = '')
    {
        $out =  array(
            'message'   => $message,
            'code'      => $code,
            'type'      => $type
        );

        $this->getReturn($out);
    }

    /**
     * 返回数据
     *
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function getReturn($data,$type='JSON')
    {

        switch (strtoupper($type)) {
            case 'JSON':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data, $json_option));
            case 'XML':
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit(xml_encode($data, 'jkcms'));
            default:
                $this->error();
        }
    }

    /**
     * 将多少数组转换一维数组，不保留键
     * @param $arr     二维数据以上
     * @return array   返回一维数组
     */
    public function arrayChange($arr){
        static $return;
        foreach($arr as $v)
        {
            if(is_array($v))
            {
                $this->arrayChange($v);
            }else{

                $return[]=$v;
            }
        }

        return $return;
    }

    /**
     * 生成返回值参数
     * @return array
     */
    public function returns()
    {
         return $return = array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');
    }



    /**
     * 生成用户加密唯一口令
     * @return string   返回32位加密
     */
    function get_user_token()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        return md5(date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT));
    }


    /**
     * 获取短信验证码
     * @param $phone 手机号
     * @return array  返回手机号和验证码 mobile=>手机号   mobile_code=》短信验证码
     */
    public function verification($phone)
    {
        import("Vendor.IHuYiHelper");

        $sms = new \IHuYiHelper(C('HY_ACCOUNT'),C('HY_PASSWORD'));
        $send = $sms->sendRegisterCode($phone);
        return $send;
    }



    /**
     * 极光推送（推送到用户端）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_client($regId,$content,$title){
        import("Org.JPush.JPush");
        $client = new \JPush(C('CLIENT_APPKEY'), C('CLIENT_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addRegistrationId($regId)
            ->addAndroidNotification($content, $title, 1,array('type'=>'grab','id'=>'value2'))
            ->addIosNotification($content, 'sound.mp3', '+1', true, 'iOS category', array("type"=>"grab", "id"=>"10"))
            ->setOptions(123333, 864000, null, C('JPUSH_SET'))
            ->setMessage($content, $title, $content_type=null, $extras=null)
            ->send();
    }


    /**
     * 极光推送（推送到物流端）161a3797c8072286576   120c83f76027552ea60
     * @param $type string  字段类型：grab表示抢单推送  $id则是订单id
     * @param $type string  字段类型：quit表示下线通知
     */
    public function bbs_push_server($regId,$content,$title, $type = '', $id = 0){
        import("Org.JPush.JPush");
        $client = new \JPush(C('SERVER_APPKEY'), C('SERVER_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addRegistrationId($regId)
            ->addAndroidNotification($content, $title, 1,array('type'=>$type,'id'=>$id))
            ->addIosNotification($content, 'sound.mp3', '+1', true, 'iOS category', array("type"=>$type, "id"=>$id, 'content'=>$title))
            ->setOptions(123333, 864000, null, C('JPUSH_SET'))
            ->setMessage($content, $title, $content_type=null, $extras=null)
            ->send();
    }


    /**
     * 极光推送（推送到所有）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_all(){
        import("Org.JPush.JPush");
        $client = new \JPush(C('CLIENT_APPKEY'), C('CLIENT_MASTER'));
//        $client = new \JPush(C('SERVER_APPKEY'), C('SERVER_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert('这是推送测试')
            ->send();
    }





    /**
     * 极光推送（推送到用户端）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_client_voice($regId,$content,$title,$type = ''){
        import("Org.JPush.JPush");
        $client = new \JPush(C('CLIENT_APPKEY'), C('CLIENT_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addRegistrationId($regId)
            ->addAndroidNotification($content, $title, 1,array('type'=>$type))
            ->addIosNotification($content, 'sound', '+1', true, 'iOS category', array("type"=>$type))
            ->setOptions(123333, 864000, null, C('JPUSH_SET'))
            ->setMessage($content, $title, $content_type=null, $extras=null)
            ->send();
    }


    /**
     * 极光推送（推送到物流端）161a3797c8072286576   120c83f76027552ea60
     * @param $type string  字段类型：grab表示抢单推送  $id则是订单id
     * @param $type string  字段类型：quit表示下线通知
     */
    public function bbs_push_server_voice($regId,$content,$title, $type = '', $id = 0){
        import("Org.JPush.JPush");
        $client = new \JPush(C('SERVER_APPKEY'), C('SERVER_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addRegistrationId($regId)
            ->addAndroidNotification($content, $title, 1,array('type'=>$type,'id'=>$id))
            ->addIosNotification($content, 'sound', '+1', true, 'iOS category', array("type"=>$type, "id"=>$id, 'content'=>$title))
            ->setOptions(123333, 864000, null, C('JPUSH_SET'))
            ->setMessage($content, $title, $content_type=null, $extras=null)
            ->send();
    }
}

