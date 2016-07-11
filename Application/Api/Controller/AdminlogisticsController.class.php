<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/19
 * Time: 20:58
 */

namespace Api\Controller;
use Think\Model;

/**
 * Class AdminlogisticsController
 * @package Api\Controller
 * 物流端用户模块接口
 */
class AdminlogisticsController extends LogisticsCommonController
{

    /**
     * 用户注册短信验证码
     * @param phone    注册填写的手机号
     * @return int          验证码
     */
    public function noteVerify()
    {
        $return = $this->returns(); // array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');

        $phone = I('request.phone');

        if(empty($phone)){
            $return['message'] = '手机号码不能为空!';
            $this->getReturn($return);
        }
        if(!preg_match("/^1[2-8][0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phone)){
            //手机号码格式不对
            $return['message'] = '手机号码格式错误';
            $this->getReturn($return);
        }

//        if(M('logistics')->where(array('logistics_phone'=>array('eq',$phone)))->count()){
////            $return['message'] = '此手机号已注册！';
//            $return['message'] = '您的号码已被注册，为保障您的权益，请致电xxxx-xxxx，或进入个人中心-设置-给我们建议反馈，我们将为您核实解决';
//            $this->getReturn($return);
//        }

        //获取验证码
        $send = $this->verification($phone);
        if(!$send['mobile_code']){
            $return['data'] = array();
            $return['message'] = '获取验证码失败！';
            $this->getReturn($return);
        }

        $return['status'] = 1;
//        $return['data']['verify'] = $send['mobile_code'];
        $return['data']['phone'] = $send['mobile'];
        $_SESSION['phone'] = $send['mobile'];
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
        $verify   = I('request.input_verify');
        $password   = I('request.password');
        $againpass   = I('request.againpass');

        $LogiModel = D('logistics');
        if($LogiModel->where(array('logistics_phone'=>array('eq',$phone)))->count()){
//            $return['message'] = '此手机号已注册！';
            $return['message'] = '您的手机已被注册，为保护您的权益，请致电免费客服电话4008097898';
            $return['data']['phone'] = '4008097898';
            $this->getReturn($return);
        }

        if($phone != $_SESSION['phone']){
            $return['message'] = '手机号码不匹配！';
            $return['data']['phone'] = '';
            $this->getReturn($return);
        }

        //判断验证码是否正确
        if($_SESSION['verify'] != $verify){
            $return['message'] = '验证码错误';
            $return['data']['phone'] = '';
            $this->getReturn($return);
        }
        //判断密码是否匹配
        if(!Judge($password,$againpass)){
            $return['message'] = '密码不一致';
            $return['data']['phone'] = '';
            $this->getReturn($return);
        }

        //组合数据 添加用户
        $add['logistics_phone']      =      $phone;
//        $add['logistics_name']       =      $phone;
        $add['logistics_password']   =      $password;
        $add['jpush_id']             =      I('request.jpush_id');
        $add['logistics_status']     =      1;
        $add['token']                =      $this->get_user_token();
        $add['is_register_type']     =      1;

        $insert_as = $LogiModel->add($add);
        if($insert_as === false){
            $retrun['message'] = '参数错误，请重试';
        }
        //生成消息记录
        $messInfo['content'] = '注册成功，等待后台审核';
        $messInfo['title'] = '等待审核';
        $messInfo['logistics_id'] = $insert_as;
        D('logimess')->InsertMess($insert_as,$messInfo['content'],$messInfo['title']);
        //发送推送
        $this->bbs_push_server($add['jpush_id'],$messInfo['content'],$messInfo['title']);

        //注册成功返回登录信息
        $data = $LogiModel->getimgDomainInfo($insert_as);

        $return['data'] = $data;
        $return['data']['phone'] = '';
        $return['status'] = 1;
        $return['message'] = '注册成功，等待后台审核！';
        unset($_SESSION['phone']);
        unset($_SESSION['verify']);
        $this->getReturn($return);
    }


    /**
     * 物流公司通过验证注册了手机号后填写资料
     *
     */
    public function writeInfo()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $Logi = D("Logistics");

        //先将原来信息查询出来
        $list = $Logi->find($this->logi_id);
        if($list['logistics_status'] == 0){
            $result['message'] = '非法操作';
            $this->getReturn($result);
        }

        //接收
        $data['logistics_per_name']     = I('request.per_name');    //法人真实姓名
        $data['logistics_name']         = I('request.name');        //公司名称
        $data['logistics_tel']        = I('request.tel');       //公司联系电话
        $data['logistics_address']      = I('request.address'); //公司地址
        //接受图片
        $info = $this->uploadImg();
        $data['logistics_person_img']   = $info['person_img'];    //法人照片
        $data['logistics_open_img']     = $info['open_img'];        //营业执照
        $data['logistics_check_img']    = $info['check_img'];  //税务登记证
        $data['logistics_way_img']      = $info['way_img'];      //道路许可证
        $data['logistics_img']          = $info['img'];              //公司照片

        //修改
        $data['logistics_id'] = $this->logi_id;
        $as = $Logi->save($data);
        if($as === false){
            $result['code']  = 11;
            $result['message']  = '信息不全，提交失败！';
            $result['sql']  = $Logi->getLastSql();
            $this->getReturn($result);
        }

        //保存成功后，删除原有图片
        unlink('Public/'.$list['logistics_person_img']);
        unlink('Public/'.$list['logistics_open_img']);
        unlink('Public/'.$list['logistics_check_img']);
        unlink('Public/'.$list['logistics_way_img']);
        unlink('Public/'.$list['logistics_img']);

        //注册成功返回登录信息
        $res = $Logi->getimgDomainInfo($this->logi_id);

        $result['data'] = $res;
        $result['status'] = 1;
        $result['message'] = '提交资料成功';
        $this->getReturn($result);
    }


    /**
     * 物流端登录
     * @param user_phone     用户手机号
     * @param user_password 用户密码（经过md5后的）
     */
    public function logiLogin()
    {
        $return = $this->returns();

        $phone = I('request.phone');     //接受输入用户名和密码
        $pass = I('request.password');
        $jpush_id = I('request.jpush_id');  //极光id

        if(empty($phone) || empty($pass)){
            $return['message'] = '账号密码不能为空';
            die($this->getReturn($return));
        }
        $Logi = M('logistics');
        $map['logistics_phone']       = $phone;
        $data = $Logi->where($map)->find();

        if(empty($data)){
            $return['message'] = '手机号不存在！';
            $this->getReturn($return);
        }

        if($data['logistics_password'] != $pass){
            $return['message'] = '密码错误！';
            $this->getReturn($return);
        }

        //判断极光
        if($data['jpush_id'] != $jpush_id){

//            $content = '您的账号在别的设备登录了';
//            D('Logimess')->InsertMess($data['logistics_id'],$content, $content);
//            $this->bbs_push_server_voice($data['jpush_id'],$content,$content,'quit');

            $save['jpush_id'] = $jpush_id;
            $save['logistics_id'] = $data['logistics_id'];
            $Logi->save($save);
        }

        //判断token
        if($data['token'] == ''){
            $token['logistics_id'] = $data['logistics_id'];
            $token['token'] = $this->get_user_token();
            $Logi->save($token);
            $newToken = $Logi->field('token')->find($token['logistics_id']);
            $data['token'] = $newToken['token'];
        }


        $this->token = $data['token'];
        $this->logi_id = $data['logistics_id'];
        $data['logistics_person_img'] = imgDomain($data['logistics_person_img']);
        $data['logistics_open_img'] = imgDomain($data['logistics_open_img']);
        $data['logistics_check_img'] = imgDomain($data['logistics_check_img']);
        $data['logistics_way_img'] = imgDomain($data['logistics_way_img']);
        $data['logistics_img'] = imgDomain($data['logistics_img']);
        if($data['logistics_head_img'] == ''){
            $data['logistics_head_img'] = $data['logistics_img'];
        }else{
            $data['logistics_head_img'] = imgDomain($data['logistics_head_img']);
        }
        $return['status'] = 1;
        $return['data'] = $data;
        $return['token'] = $this->token;
        $this->getReturn($return);

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
        $upload->savePath  =     'Uploads/logistics/'; // 设置附件上传（子）目录
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
     * 图片上传，支持多图片上传
     * @return array    返回格式： 如果上传文件是：'$_FILES['name']'   则  'name' => '文件路径';
     */
    public function uploadImgVeh(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public/'; // 设置附件上传根目录
        $upload->savePath  =     'Uploads/vehicle/'; // 设置附件上传（子）目录
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
     * 个人信息
     */
    public function logiInfo()
    {
        $this->checkLogistics();
        $result = $this->returns();

        //个人信息
        $data = D('logistics')->find($this->logi_id);
        $data['logistics_person_img'] = imgDomain($data['logistics_person_img']);
        $data['logistics_open_img'] = imgDomain($data['logistics_open_img']);
        $data['logistics_check_img'] = imgDomain($data['logistics_check_img']);
        $data['logistics_way_img'] = imgDomain($data['logistics_way_img']);
        $data['logistics_img'] = imgDomain($data['logistics_img']);
        if(empty($data['logistics_head_img'])){
            $data['logistics_head_img'] = $data['logistics_img'];
        }
        $result['data']['info'] = $data;

        //长跑路线
        $wireData = D('wire_relation')->alias('R')
            ->join('dhh_car_wire C ON R.wire_id = C.wire_id')
            ->join('dhh_region Reg ON C.wire_state = Reg.region_id')
            ->join('dhh_region Re ON C.wire_end = Re.region_id')
            ->field('R.rela_id,R.predict_time,R.price,R.gently,Reg.region_name as start,Re.region_name as end')
            ->where('R.logistics_id = '.$this->logi_id)
            ->select();
        $result['wire'] = $wireData;
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 添加路线
     *
     */
    public function addWire()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();

        $depart = I('request.depart_city'); //起点
        $des = I('request.des_city');   //终点
        $time = I('request.time');
        $price = I('request.price');    //轻货
        $gently = I('request.gently');  //重货

        $Region = D('region');
        $WireRelation = D('wire_relation');
        $Wire = M('car_wire');

        //判断地址
        $depart = $Region->getId($depart,2);
        $des = $Region->getId($des,2);

        if($depart && $des){
            $map['wire_state'] = $depart;
            $map['wire_end'] = $des;
            $combo = $Wire->where($map)->find();    //判断路线是否有了
            if(!$combo){    //如果没有就添加路线
                $temp['wire_state'] = $depart;
                $temp['wire_end'] = $des;
                $temp['wire_effect'] = 1;
                $wire_id = $Wire->add($temp);
            }else{
                $wire_id = $combo['wire_id'];
            }

            $info['logistics_id'] = $this->logi_id;
            $info['wire_id'] = $wire_id;
            //判断是否重复添加（关系表里面的重复）
            $selIF = $WireRelation->where($info)->select();
            if($selIF){
                $result['message'] = '路线已添加，无需重复添加！';
                $result['status'] = 1;
                $this->getReturn($result);
            }
            $info['predict_time'] = $time;
            $info['price'] = $price;
            $info['gently'] = $gently;
            if(!$WireRelation->create($info)){
                $result['data'] = $WireRelation->getError();
                $this->getReturn($result);
            }
            $insertId = $WireRelation->add();
            if($insertId){
                $result['message'] = '添加成功';
                $result['status'] = 1;
                $this->getReturn($result);
            }else{
                $result['message'] = '添加数据库失败！';
                $result['code'] = 105;
                $this->getReturn($result);
            }
        }else{
            $result['message'] = '路线地址不存在';
            $this->getReturn($result);
        }

    }


    /**
     * 忘记密码获取验证码
     * @param phone 手机号
     */
    public function forgetVerify()
    {
        $result = $this->returns();
        $phone = I('request.phone');    //接收手机号

        $Logi = D('logistics');
        $judge = $Logi->where('logistics_phone = '.$phone)->count();
        if(!$judge){
            $result['message'] = '该手机号还未注册！';
            $this->getReturn($result);
        }

        //获取验证码
        $send = $this->verification($phone);
        if(!$send['mobile_code']){
            $result['message'] = '获取验证码失败！';
            $result['data'] = array();
            $this->getReturn($result);
        }

        $result['message'] = '返回验证码';
        $result['status'] = 1;
        $result['data']['verify'] = $send['mobile_code'];
        $result['data']['phone'] = $send['mobile'];
        $_SESSION['phone'] = $send['mobile'];
        $_SESSION['verify'] = $send['mobile_code'];
        $this->getReturn($result);
    }


    /**
     * 忘记密码判断验证码是否正确
     * @param phone 手机号
     */
    public function judgeVerify()
    {
        $result = $this->returns();
        $verify = I('request.verify');    //接收手机号
        $phone = I('request.phone');    //接收手机号

        if($phone != $_SESSION['phone']){
            $result['message'] = '验证码错误！';
            $this->getReturn($result);
        }


        if($verify != $_SESSION['verify']){
            $result['message'] = '验证码错误！';
            $this->getReturn($result);
        }

        $result['message'] = '验证码正确！';
        $result['status'] = 1;
        unset($_SESSION[$phone]);
        $this->getReturn($result);
    }


    /**
     * 修改登录密码
     * @param password
     * @param phone
     */
    public function modifyPass()
    {
        $result = $this->returns();

        $phone = I('request.phone');
        $pass = I('request.password');

        if(empty($phone) || empty($pass)){
            $result['message'] = '手机号和密码都不能为空！';
            $this->getReturn($result);
        }

        $Logi = D('logistics');
        $data = $Logi->field('logistics_id')->where('logistics_phone = '.$phone)->find();
        if(empty($data)){
            $result['message'] = '用户不存在！';
            $this->getReturn($result);
        }

        $data['logistics_password']  = $pass;
        $judge = $Logi->save($data);
        if($judge === false){
            $result['message'] = '密码修改失败，请重试！';
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['message'] = '密码修改成功！';
        $this->getReturn($result);
    }



    /**
     * 个人中心--添加车辆信息
     */
    public function addVehicleInfo()
    {
        $result = $this->returns();

        $Par = D('parameter');  //品牌型号表
        $Select = D('select');  //类型选择表

        //品牌  、型号
        $ParData = $Par->where('parent_id = 0 AND status = 1')->select();

        foreach($ParData as $index=>$item)
        {
            $info = array();
            $info = $Par->where('parent_id = '.$item['id'].' AND status = 1')->select();
            if($info){
                $ParData[$index]['data'] = $info;
            }else{
                $ParData[$index]['data'] = (Object)array();
            }
        }

        //货箱形式
        $data = $Select->field('select_area_state')->where('type_id = 5')->select();
        $newInfo = array();
        foreach($data as $key=>$val){
            $cargo['type'] = $val['select_area_state'];
            $newInfo[] = $cargo['type'];
        }

        $result['data']['parameter'] = $ParData;
        $result['data']['cargo'] = $newInfo;
        $result['status'] = 1;
        $this->getReturn($result);

    }


    /**
     * 个人中心--添加车辆
     */
    public function addVehicle()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $Veh = D('vehicle');
        $data['logistics_id'] = $this->logi_id;
        $data['vehicle_series'] = I('request.series');  //车系列
        $data['vehicle_type'] = I('request.type');      //车型号
        $data['vehicle_car_weight'] = I('request.car_weight');      //车重
        $data['vehicle_weight'] = I('request.weight');              //载重
        $data['vehicle_long'] = I('request.long');                  //车长
        $data['vehicle_height'] = I('request.height');              //车高
        $data['vehicle_age'] = I('request.age');                    //车龄
        $data['vehicle_cargo_long'] = I('request.cargo_long');      //车箱长
        $data['vehicle_cargo_weight'] = I('request.cargo_weight');  //车箱宽
        $data['vehicle_cargo_height'] = I('request.cargo_height');  //车箱高
        $data['vehicle_intro'] = I('request.intro');                //车简介
        $data['vehicle_cargo_type'] = I('request.cargo_type');     //卸货形式
        $data['vehicle_licence'] = I('request.licence');           //车牌

        $info = $this->uploadImgVeh();
        $data['vehicle_car_img']   = $info['car_img'];    //车照片
        $data['vehicle_licence_img']     = $info['licence_img'];        //车牌照
        $data['vehicle_year_img']    = $info['year_img'];  //车年审照片
        $data['vehicle_way_img']      = $info['way_img'];      //车道路许可证

        if(!$Veh->create($data))
        {
            $result['data'] = $Veh->getError();
            $this->getReturn($result);
        }
        $combo = $Veh->add($data);
        if($combo === false){
            $result['code']  = 11;
            $result['message']  = '信息不全，添加失败！';
            $result['sql']  = $Veh->getLastSql();
            $this->getReturn($result);
        }
        $result['status'] = 1;
        $result['data'] = (Object)array();
        $result['message'] = '添加成功！';
        $this->getReturn($result);

    }


    /**
     * 个人中心--车辆管理
     * @param token
     */
    public function vehicleList()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $page_size = 6;
        $_GET['p'] = I('request.p',1);
        $count = M('vehicle')->where('logistics_id = '.$this->logi_id)->count();

        if(!$count){
            $result['message'] = '暂时还没有车辆,快去添加吧！';
            $result['data'] = array();
            $result['status'] = 1;
            $this->getReturn($result);
        }

        $Page = new \Think\Page($count,$page_size);
        $data = M('vehicle')->field('vehicle_id,vehicle_series,vehicle_name,vehicle_licence,vehicle_car_weight,vehicle_weight,vehicle_car_img')->where('logistics_id = '.$this->logi_id)->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach($data as $key=>$val){
            $data[$key]['vehicle_car_img'] = imgDomain($val['vehicle_car_img']);
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        $result['status'] = 1;
        $result['data'] = $data;
        $this->getReturn($result);
    }


    /**
     * 修改个人信息
     * @param key   要修改的字段
     * @param value 要修改的值
     */
    public function changeInfo()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $key = I('request.key');
        $val = I('request.value');

        $Logi = D('logistics');

        $map['logistics_id'] = $this->logi_id;
        switch($key)
        {
            case 'img':
                $info = $this->uploadImg();
                $map['logistics_head_img'] = $info['value'];
                break;
            case 'brief':
                $map['logistics_brief'] = $val;
                break;
            default:
                $result['message'] = '参数错误';
                $this->getReturn($result);
                break;
        }
        $combo = $Logi->save($map);
        if($combo === false){
            $result['code']  = 11;
            $result['message']  = '信息不全，提交失败！';
            $result['sql']  = $Logi->getLastSql();
            $this->getReturn($result);
        }

        if($key == 'img'){
            $result['data'][$key] = imgDomain($info['value']);
        }else{
            $result['data'][$key] = $val;
        }

        $result['status'] = 1;
        $result['message'] = '修改成功！';
        $this->getReturn($result);
    }


    /**
     * 根据原密码修改密码
     * @param token
     * @param oldPass 原密码
     * @param newPass 新密码
     */
    public function oldPass()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $old = I('request.oldpass');
        $new = I('request.newpass');

        $Logistics = D('logistics');

        $info['logistics_id'] = $this->logi_id;

        $data = $Logistics->field('logistics_password')->find($info['logistics_id']);
        //匹配原密码是否正确
        if($data['logistics_password'] !== $old){
            $result['message'] = '原密码不正确';
            $this->getReturn($result);
        }

        //更新密码
        $info['logistics_password'] = $new;
        $as = $Logistics->save($info);
        if(false === $as){
            $result['message'] = "修改失败，请重试！";
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['message'] = '修改成功';
        $this->getReturn($result);
    }


    /**
     * 消息中心
     */
    public function messageCentre()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $_GET['p'] = I('request.p',1);    //分页
        $page_size  = C('PAGE_SIZE');    //每页显示条数
        $logimess = M('logimess');

        $count = $logimess->where('logistics_id = '.$this->logi_id.' AND status = 1')->count();
        $Page = new \Think\Page($count,$page_size);
        $data = $logimess->where('logistics_id = '.$this->logi_id.' AND status = 1')->limit($Page->firstRow.','.$Page->listRows)->order('time desc')->select();
        //判断有没有消息
        if(empty($data)){
            $result['message'] = '暂时还没有消息';
            $result['status'] = 1;
            $result['data'] = array();
            $this->getReturn($result);
        }

        foreach($data as $key=>$val){
            $data[$key]['message_id'] = $val['id'];
            $data[$key]['time'] = date('Y-m-d',$val['time']);
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        $result['status'] = 1;
        $result['data'] = $data;
        $this->getReturn($result);
    }


    /**
     * 个人中心--意见反馈
     */
    public function giveSugg()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();

        $data['content'] = I('post.content');
        $data['usertype'] = 2;
        $data['member_id'] = $this->logi_id;
        $data['status'] = 0;
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
        $result['logiInfoage'] = '发送成功，您的宝贵建议我们一定会改善！';
        $this->getReturn($result);
    }



}