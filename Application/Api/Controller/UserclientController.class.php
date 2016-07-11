<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/14
 * Time: 14:44
 */

namespace Api\Controller;
use Think\Controller;

/**
 * 客户端个人中心接口控制器
 * Class UserclientController
 * @package Api\Controller
 */
class UserclientController extends ClientCommonController
{

    /**
     * 用户注册短信验证码
     * @param user_phone    注册填写的手机号
     * @return int          验证码
     */
    public function noteVerify()
    {
        $return = $this->returns(); // array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');

        $user_phone = I('request.user_phone');
        if(empty($user_phone)){
            $return['message'] = '手机号码不能为空!';
            $this->getReturn($return);
        }
        if(!preg_match("/^1[2-9][0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$user_phone)){
            //手机号码格式不对
            $return['message'] = '手机号码格式错误';
            $this->getReturn($return);
        }

        if(M('users')->where(array('user_phone'=>array('eq',$user_phone)))->count()){
            $return['message'] = '此手机号已注册！';
            $this->getReturn($return);
        }

        $send = $this->verification($user_phone);
        if(!$send['mobile_code']){
            $return['data'] = array();
            $return['message'] = '获取验证码失败！';
            $this->getReturn($return);
        }


        $return['data']['verify'] = $send['mobile_code'];
        $return['data']['user_phone'] = $send['mobile'];
        $return['status'] = 1;
        $return['message'] = '获取验证码成功！';
        $_SESSION['verify'] = $send['mobile_code'];
        $this->getReturn($return);
    }

    /**
     * 用户注册
     */
    public function register()
    {
        $return = $this->returns();
        //接受参数，用户输入验证码和原验证码，两次密码
        $phone  = I('request.phone');       //手机号
        $input  = I('request.input_verify'); //用户输入的验证码
        $pass   = I('request.password');
        $username   = I('request.user_name');

        //验证手机格式
        if(!preg_match("/^1[2-9][0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phone)){
            //手机号码格式不对
            $return['message'] = '手机号码格式错误';
            $this->getReturn($return);
        }

        //判断验证码是否正确
        if($_SESSION['verify'] != $input){
            $return['message'] = '验证码错误';
            $this->getReturn($return);
        }
        //判断登录密码不能为空
        if(empty($pass))
        {
            $return['message'] = '密码不能为空';
            $this->getReturn($return);
        }

        $Users = D('users');

        //生成唯一口令
        $data['token'] = $this->get_user_token();
        $data['user_password'] = $pass;
        $data['user_phone']    = $phone;
        $data['user_status']   = 1;
        $data['user_name']  = $username;
        $data['session_time'] = time();

        //验证字段
        $Users->startTrans();   //开启事务
        if(!$Users->create($data)){
            $return['message'] = $Users->getError();
            $this->getReturn($return);
        }
        //如果验证通过
        $inserID = $Users->add($data);
        if(!$inserID){
            $return['message'] = '注册失败，验证没通过';
            $this->getReturn($return);
        }

        //写入极光id
        $save['user_id'] = $inserID;
        $save['jpush_id'] = I('request.jpush_id');
        $as = $Users->save($save);
        if($as === false){
            $return['message'] = '注册失败，请重试！';
            $return['code'] = 77;
            $this->getReturn($return);
        }

        $Users->commit();

        //返回注册信息
        $info = $Users->find($inserID);
        $return['status'] = 1;
        $return['message'] = '注册成功';
        $return['token'] = $data['token'];
        $return['data'] = $info;
        $this->getReturn($return);

    }




    /**
     * 用户登录
     * @param user_phone     用户手机号
     * @param user_password 用户密码（经过md5后的）
     */
    public function userLogin()
    {
        $phone = I('request.user_phone');     //接受输入用户名和密码
        $pass = I('request.user_password');
        $jpush_id = I('request.jpush_id');  //极光id

        $return = $this->returns();
        $User = M('users');
        if(empty($phone) || empty($pass)){
            $return['message'] = '账号密码不能为空';
            die($this->getReturn($return));
        }

        $map['user_phone']       = $phone;
        $user_data = $User->where($map)->find();//查询出此手机是否存在
        if($user_data){
            //如果手机用户存在，则匹配密码是否一致
            if($user_data['user_password'] == $pass)
            {
                //判断极光
                if($user_data['jpush_id'] != $jpush_id){
                    if(!empty($user_data['jpush_id'])){
                        $content = '您的账号在其他设备登录了';
                        D('Message')->InsertMess($user_data['user_id'], $content,$content);
                        $this->bbs_push_client_voice($user_data['jpush_id'], $content, $content, 'quit');
                    }
                    $save['jpush_id'] = $jpush_id;
                    $save['user_id'] = $user_data['user_id'];
                    $User->save($save);
                }

                //刷新session会话时间
                $this->refresh($user_data['user_id']);

                $return['status'] = 1;
                $user_data['user_img'] = imgDomain($user_data['user_img']);
                $return['data'] = $user_data;
                $this->getReturn($return);
            }else{
                $return['message'] = '密码错误！';
                $this->getReturn($return);
            }
        }else{
            $return['message'] = '用户不存在';
            $this->getReturn($return);
        }

    }


    /**
     * 用户注册短信验证码
     * @param user_phone    注册填写的手机号
     * @return int          验证码
     */
    public function changeVerify()
    {
        $return = $this->returns(); // array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');

        $user_phone = I('request.user_phone');
        if(empty($user_phone)){
            $return['message'] = '手机号码不能为空!';
            $this->getReturn($return);
        }
        if(!preg_match("/^1[2-9][0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$user_phone)){
            //手机号码格式不对
            $return['message'] = '手机号码格式错误';
            $this->getReturn($return);
        }


        $send = $this->verification($user_phone);
        if(!$send['mobile_code']){
            $return['data'] = array();
            $return['message'] = '获取验证码失败！';
            $this->getReturn($return);
        }


        $return['data']['verify'] = $send['mobile_code'];
        $return['data']['user_phone'] = $user_phone;
        $return['status'] = 1;
        $return['message'] = '获取验证码成功！';
        $_SESSION['verify'] = $send['mobile_code'];
        $_SESSION['phone'] = $send['mobile'];
        $this->getReturn($return);
    }


    /**
     * 判断修改密码的验证码
     * @param phone
     * @param verify
     */
    public function modifyPass()
    {
        $result = $this->returns();

        $phone = I('request.phone');
        $verify = I('request.verify');

        if($phone != $_SESSION['phone']){
            $result['message'] = '手机号码不匹配！';
            $this->getReturn($result);
        }

        //判断验证码是否正确
        if($_SESSION['verify'] != $verify){
            $result['message'] = '验证码错误';
            $this->getReturn($result);
        }


        $result['status'] = 1;
        $result['message'] = '验证成功！';
        unset($_SESSION['phone']);
        unset($_SESSION['verify']);
        $this->getReturn($result);
    }


    /**
     * 修改登录密码
     * @param new_password 新密码
     * @param phone 确认密码
     * @param verify 验证码
     */
    public function updatePass()
    {
        $new_pass   = I('request.password');
        $phone   = I('request.phone');
        $verify   = I('request.verify');
        $return = $this->returns();

        if(!$new_pass){
            $return['message'] = '新密码不能为空！';
            die($this->getReturn($return));
        }

        //判断验证码是否正确
        if($_SESSION['verify'] != $verify){
            $return['message'] = '验证码错误';
            $this->getReturn($return);
        }

        $map['user_password'] = $new_pass;
        $res = M('users')->where('user_phone='.$phone)->save($map);
        if($res !== false){
            $return['status'] = 1;
            $return['message'] = '修改成功！';
            die($this->getReturn($return));
        }else{
            $return['message'] = '修改失败，请重试！';
            die($this->getReturn($return));
        }
    }


    /**
     * 根据原密码修改密码
     * @param token
     * @param oldPass 原密码
     * @param newPass 新密码
     */
    public function oldPass()
    {
        $this->checkUser();
        $result = $this->returns();

        $old = I('request.oldpass');
        $new = I('request.newpass');

        $User = D('users');

        $info['user_id'] = $this->user_id;
        $data = $User->field('user_password')->find($info['user_id']);
        //匹配原密码是否正确
        if($data['user_password'] !== $old){
            $result['message'] = '原密码不正确';
            $this->getReturn($result);
        }

        //更新密码
        $info['user_password'] = $new;
        $as = $User->save($info);
        if(false === $as){
            $result['message'] = "修改失败，请重试！";
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['message'] = '修改成功';
        $this->getReturn($result);
    }


    /**
     * 退出登录
     */
    public function exitregi()
    {
        $this->exitUser();
        $result = $this->returns();

        $result['status'] = 1;
        $result['message'] = '退出成功！';
        $this->getReturn($result);
    }


    /**
     * 个人信息
     * @param $id   用户id
     * @return array    用户信息
     */
    public function getUserInfo()
    {
        $id = I('request.id');
        $user_data = M('users')->field('user_id,user_name,user_age,user_phone,user_img,user_sex')->where('user_status = 1')->find($id);
//        var_dump($_SERVER);die;
        if($user_data['user_img']){
            $user_data['user_img'] =  $_SERVER['HTTP_HOST'].'/Public/'.$user_data['user_img'];
        }
        $this->getReturn($user_data);
    }


    /**
     * 预约发货
     *
     */
    public function shipments()
    {
        $this->checkUser();
        $return = $this->returns();  //array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '')
        $return['token'] = $this->token;
        $data = I('post.');
        $special = I('request.special');    //特殊，是否指定东华行物流公司
        $inform = I('request.inform');         //是否通知此路线的所有公司
        $data['user_id'] = $this->user_id;
        $Orders = D('orders');
        $Region = D('region');
        if (!$Orders->create($data)){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $return['message'] = $Orders->getError();
            $this->getReturn($return);
        }else{
            //特殊
            if(I('request.special') == 1){
                $data['logistics_id'] = D('Logistics')->getSpecial();
                $data['order_type'] = 2;  //1：预约发货，2：定向发货
            }else{
                $data['order_type'] = 1;  //1：预约发货，2：定向发货
            }
            //通过验证，表示字段都不为空,查询城市名称id
            //起始地省
            $where['region_name'] = array('like',"%$data[order_depart_province]%");
            $where['region_type'] = 1;
            $proId = $Region->where($where)->find();
            if(!$proId){
                $return['message'] = '起始地省份不存在';
                $return['code'] = 301;
                $this->getReturn($return);
            }
            //起始地市
            $where['region_name'] = array('like',"%$data[order_depart_city]%");
            $where['region_type'] = 2;
            $startCity = $Region->field('region_id')->where($where)->find();
            if(!$startCity){
                $return['message'] = '起始地市区不存在';
                $return['code'] = 302;
                $this->getReturn($return);
            }
            //目的地省
            $where['region_name'] = array('like',"%$data[order_des_province]%");
            $where['region_type'] = 1;
            $proId1 = $Region->where($where)->find();
            if(!$proId1){
                $return['message'] = '目的地省份不存在';
                $return['code'] = 303;
                $this->getReturn($return);
            }
            //目的地市
            $where['region_name'] = array('like',"%$data[order_des_city]%");
            $where['region_type'] = 2;
            $endCity = $Region->field('region_id')->where($where)->find();
            if(!$endCity){
                $return['message'] = '目的地市区不存在';
                $return['code'] = 304;
                $this->getReturn($return);
            }

            $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');//订单号
            $data['order_sn'] = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(microtime(), 2, 5);
            $data['order_status'] = 1;  //1 = 未确定  2 = 未完成  3 = 已完成
            $data['order_time'] = strtotime($data['order_time']);   //格式化时间
            $data['order_add_time'] = time();   //格式化时间
            $insertID = $Orders->add($data);
            if($insertID){
                $LogiMessModel = D('Logimess');
                /* 预约成功  判断是否需要通知 */
                if($inform == 1 && $special == 0 && $data['logistics_id'] == NULL){
                    $map['wire_state']  =   $startCity['region_id'];
                    $map['wire_end']    =   $endCity['region_id'];
                    $map['wire_effect'] =   1;
                    if($pushArr = M('CarWire')->field('wire_id')->where($map)->find()){
                        $pushData = D('WireRelation')
                            ->alias('wr')
                            ->join('__LOGISTICS__ l ON l.logistics_id = wr.logistics_id')
                            ->where('wr.wire_id = '.$pushArr['wire_id'])
                            ->field('l.jpush_id, l.logistics_id')
                            ->select();
                        if($pushData){
                            $title = '有人发布一条订单路线和你相关，可以到抢单大厅抢单！';
                            $content = "{$data['order_sn']} 订单是预约订单，路线是{$data['order_depart_city']}--{$data['order_des_city']} 等待抢单中……";

                            foreach($pushData as $key=>$val){
                                $LogiMessModel->InsertMess($val['logistics_id'],$content,$title);
                                if($val['jpush_id']){
                                    $this->bbs_push_server($val['jpush_id'],$content,$title,'grab', $insertID);
                                }
                            }
                        }
                    }
                }

                if($special == 1){
                    $Jpush_id = D('Logistics')->getJPush($data['logistics_id']);
                    $content = "订单号为：{$data['order_sn']}，路线是{$data['order_depart_city']}--{$data['order_des_city']}";
                    $title = "接到一条指定你的订单";
                    $LogiMessModel->InsertMess($data['logistics_id'],$content,$title);
                    $this->bbs_push_server($Jpush_id,$content,$title,'grab', $insertID);
                }

                $return['message'] = '预约成功，请耐心等待物流抢单';
                $return['status'] = 1;
                $this->getReturn($return);
            }else{
                $return['message'] = '预约失败，信息不全';
                $this->getReturn($return);
            }
        }

    }


    /**
     * 个人中心--我的评论
     * @param token
     * @param p     分页
     */
    public function commentList()
    {
        $this->checkUser();
        $result = $this->returns();

        $id = $this->user_id;   //用户id
        $_GET['p'] = I('request.p');    //分页
        $page_size  = 2;    //每页显示条数
        $Logi = D('logistics');
        $Order = D('orders');

        $map['user_id'] = $this->user_id;
        $map['comment_status'] = 1;
        $count = D('comment')->where($map)->count();    //统计
        if(!$count){
            $result['message'] = '您暂时还未评论过！';
            $result['data'] = (Object)array();
            $this->getReturn($result);
        }

        $Page = new \Think\Page($count,$page_size);
        $data = D('comment')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();

        $info = array();
        $arr = array();
        foreach($data as $index=>$item){
            $info['comment_id'] = $item['comment_id'];
            //查询物流公司头像
            $info['logistics_img'] = $Logi->getLogiImg($item['logistics_id']);
            //查询评论订单
            $info['order_sn'] = $Order->orderSn($item['order_id']);
            $info['time'] = date('m-d',$item['comment_time']);
            $info['content'] = $item['comment_content'];
            $arr[] = $info;
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        $result['data'] = $arr;
        $result['status'] = 1;
        $this->getReturn($result);

    }


    /**
     * 个人中心--修改我的资料
     * @param token
     * @param maybe    修改字段
     */
    public function changeInfo()
    {
        $this->checkUser();
        $result = $this->returns();

        $key = I('request.key','');
        $value = I('request.value','');

        $User = D('users');
        $where['user_id'] = $this->user_id;
        switch($key)
        {
            //修改头像
            case 'img':
                $info = $this->uploadImg();
                $where['user_img'] = $info['value'];
                break;
            //修改姓名
            case 'name':
                $where['user_name'] = $value;
                break;
            //修改性别男
            case 'sex':
                $where['user_sex'] = $value;
                break;
            //修改年龄
            case 'age':
                $where['user_age'] = $value;
                break;
            default:
                $result['message'] = '参数错误！';
                $this->getReturn($result);
                break;
        }
        $combo = $User->save($where);
        if($combo === false){
            $result['code']  = 11;
            $result['message']  = '信息不全，提交失败！';
            $result['sql']  = $User->getLastSql();
            $this->getReturn($result);
        }

        $result['status'] = 1;
        if($key == 'img'){
            $result['data']['img'] = imgDomain($info['value']);
        }else{
            $result['data'][$key] = $value;
        }

        $result['message'] = '修改成功！';
        $this->getReturn($result);
    }



    /**
     * 中介物流
     */
    public function logiAll()
    {
        $result = $this->returns();

        //搜索信息
        $search = I('request.search');
        $_GET['p'] = I('request.p');
        $page_size = 6;
        //组合条件

        $map['logistics_name'] = array('like',"%$search%");
        $map['logistics_address'] = array('like',"%$search%");
        $map['logistics_phone'] = array('like',"%$search%");
        $map['_logic'] = 'OR';
        $where['_complex'] = $map;
        $where['logistics_status'] = 1;

        $logi = D('logistics');
        $count = $logi->field('logistics_id,logistics_name,logistics_address,logistics_img')->where($where)->count();

        $Page = new \Think\Page($count,$page_size);
        $logi_data = $logi->field('logistics_id,logistics_name,logistics_address,logistics_img')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($logi_data as $key=>$val){
            $logi_data[$key]['countV'] = $logi->countVehicle($val['logistics_id']);
            $logi_data[$key]['countW'] = $logi->countWire($val['logistics_id']);
            $logi_data[$key]['logistics_img'] = imgDomain($val['logistics_img']);
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        $result['status'] = 1;
        if($logi_data){
            $result['data'] = $logi_data;
        }else{
            $result['data'] = array();
        }
        $this->getReturn($result);
    }

    /**
     * 图片上传，支持多图片上传
     * @return array    返回格式： 如果上传文件是：'$_FILES['name']'   则  'name' => '文件路径';
     */
    public function uploadImg(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public/'; // 设置附件上传根目录
        $upload->savePath  =     'Uploads/user/'; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();

        $path = array();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            foreach($info as $key=>$file){
//                return $file['savepath'].$file['savename'];
                $path[$key] =  $file['savepath'].$file['savename'];
            }
            return $path;
        }
    }


    /**
     * 消息中心
     */
    public function messageCentre()
    {
        $this->checkUser();
        $result = $this->returns();

        $data = M('message')->where('user_id = '.$this->user_id.' AND message_status = 1')->order('message_time desc')->select();
        //判断有没有消息
        if(empty($data)){
            $result['message'] = '暂时还没有消息';
            $result['status'] = 1;
            $result['data'] = array();
            $this->getReturn($result);
        }

        foreach($data as $key=>$val){
            $data[$key]['message_time'] = date('Y-m-d',$val['message_time']);
        }

        $result['status'] = 1;
        $result['data'] = $data;
        $this->getReturn($result);
    }


    /**
     * 个人中心-给我们建议
     */
    public function giveSugg()
    {
        $this->checkUser();
        $result = $this->returns();

        $data['content'] = I('post.content');
        $data['usertype'] = 1;
        $data['status'] = 0;
        $data['member_id'] = $this->user_id;
        $data['add_at'] = time();

        $Suggest = D('suggest');
        if(false === $Suggest->create($data)){
            $result['message'] = $Suggest->getError();
            $this->getReturn($result);
        }

        $as = $Suggest->add($data);
        if(false === $as){
            $result['message'] = '发送失败，系统繁忙，请重试！';
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['message'] = '发送成功，您的宝贵建议我们一定会改善！';
        $this->getReturn($result);
    }
}





























