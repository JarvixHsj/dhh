<?php
// +----------------------------------------------------------------------
// | 广州市靖凯网络科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2015 http://jingkaigz.com 020-38038990 All rights reserved. 
// +----------------------------------------------------------------------
// | Author: Eagle <qiuwf@jingkaigz.com>
// +----------------------------------------------------------------------

/**
 * 爱互艺短信接口类
 * 封装了常用的 发送注册短信验证码（sendRegisterCode）和发送忘记密码（sendForgetCode）方法
 * 发送成功会返回手机号码和验证码，否则会返回错误代码和说明（爱互艺短信接口返回的数据）
 *
 * @author Eagle <qiuwf@jingkaigz.com>
 */
class IHuYiHelper
{
    private $account = '';
    private $password = '';
    public $content = '';
    public $mobile;
    public $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
	
	public function __construct($account = '', $password = '')
	{
		$this->account = trim($account);
		$this->password = $password;
	}

    public function sendRegisterCode($mobile = '')
    {
        if (empty($mobile)) {
            return false;
        }

        $mobileCode = self::random(6,1);
        $content = '您的验证码是：'.$mobileCode.'。请不要把验证码泄露给其他人。';
        return $this->send($content, $mobile, $mobileCode);
    }

    public function sendForgetCode($mobile = '')
    {
        if (empty($mobile)) {
            return false;
        }

        $mobileCode = self::random(6,1);
        $content = '您的验证码是：'.$mobileCode.'。请不要把验证码泄露给其他人。';
        return $this->send($content, $mobile, $mobileCode);
    }

    public function send($content, $mobile, $mobileCode)
    {
        $postData = "account=".$this->account.
                     "&password=".$this->password.
                     "&mobile=".$mobile.
                     "&content=".rawurlencode($content);

        $gets =  self::xmlToArray(self::post($postData, $this->target));

        if ($gets['SubmitResult']['code'] == 2) {
            return [
                'mobile' => $mobile,
                'mobile_code' => $mobileCode
            ];
        } else {
            return $gets['SubmitResult'];
        }
    }

    public function post($curlPost,$url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $return_str = curl_exec($curl);
        curl_close($curl);
        return $return_str;
    }

    public static function random($length = 6 , $numeric = 0)
    {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
    }

    public static function xmlToArray($xml)
    {
    	$arr = array('SubmitResult'=>null);
        $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches)){
            $count = count($matches[0]);
            for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
                if(preg_match( $reg, $subxml )){
                    $arr[$key] = self::xmlToArray( $subxml );
                }else{
                    $arr[$key] = $subxml;
                }
            }
        }
        return $arr;
    }
}
