<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/18
 * Time: 17:33
 */

namespace Api\Controller;
use Think\Controller;

/**
 * Class OrderclientController
 * @package Api\Controller
 * 客户端订单模块接口
 */
class OrderclientController extends ClientCommonController
{

    /**
     * 订单状态显示列表
     * @param token     用户口令
     * @param status    要查询的订单状态
     * @param p         页数
     * @return array    返回订单信息
     */
    public function orderList()
    {
//        检查是否登录，如果登录this->user_id  则有用户id
        $this->checkUser();
        $return = $this->returns();
        $_GET['p'] = I('request.p',1);    //请求数据页数
        $search = I('request.search');      //搜索表单
        $Orders = D('orders');
        $Region = D('region');
        $Wire = D('car_wire');
        $Logi = D('logistics');

        $status = I('request.status');  //接受查看订单的状态
        $data = $Orders->getOrderList($status,$this->user_id,C('PAGE_SIZE'),$search);        //传入订单状态，用户id，每页显示的页数, 搜索内容

        if(!$data['data']){
            $return['code'] = 10;
            $return['status'] = 1;
            $return['data'] = array();
            $return['message'] = '订单没有商品';
            $this->getReturn($return);
        }

        //循环每条订单
        foreach($data['data'] as $key=>$val){
            $data['data'][$key]['order_time'] = date('m-d',$val['order_time']);
            $data['data'][$key]['order_add_time'] = date('m-d',$val['order_add_time']);

            switch($val['order_status']){
                //1=未确定订单，说明是未确定订单，就查看是否有报价
                case 1:
                    if($val['order_type'] == 1){    //1预约  2定向
                        $offerNum = M('offer')->where('order_id = '.$val['order_id'])->count(); //查询报价数量
                        if($offerNum){  //如果报价数量不为0
                            $data['data'][$key]['show_code'] = 1;       //显示代号
                            $data['data'][$key]['show_num'] = $offerNum;
                        }else{
                            $data['data'][$key]['show_code'] = 2;
                            $data['data'][$key]['show_num'] = 0;
                        }
                    }elseif($val['order_type'] == 2){   //如果订单类型为定向发货
                        $offer['order_id'] = $val['order_id'];
                        $offer['logistics_id'] = $val['logistics_id'];
                        $condi = M('offer')->where($offer)->find(); //查看该指定物流有没有对该订单报价
                        if($condi) {
                            $data['data'][$key]['show_code'] = 4;
                            $data['data'][$key]['show_num'] = 0;
                        }else {
                            $data['data'][$key]['show_code'] = 3;
                            $data['data'][$key]['show_num'] = 0;
                        }
                    }
                    break;

                //2=未完成订单
                case 2:
                    //查询物流名称和照片
                    $logoData = $Logi->field('logistics_img,logistics_name')->find($val['logistics_id']);
                    $data['data'][$key]['logistics_img'] = imgDomain($logoData['logistics_img']);
                    $data['data'][$key]['logistics_name'] = $logoData['logistics_name'];

                    //如果为1，表示已上货正在运输中，0表示未上货
                    if($val['order_is_goods'] == 1){
                        $data['data'][$key]['show_code'] = 6;
                        $data['data'][$key]['show_num'] = 0;
                    }else{
                        $data['data'][$key]['show_code'] = 5;
                        $data['data'][$key]['show_num'] = 0;
                    }
                    break;

                //3=已完成
                case 3:
                    //查询物流名称和照片
                    $logoData = $Logi->field('logistics_img,logistics_name')->find($val['logistics_id']);
                    $data['data'][$key]['logistics_img'] = imgDomain($logoData['logistics_img']);
                    $data['data'][$key]['logistics_name'] = $logoData['logistics_name'];

                    //查看评论表是否有该用户对该订单的评论
                    $map['user_id'] = $this->user_id;
                    $map['order_id'] = $val['order_id'];
                    $comm = M('comment')->where($map)->count();
                    if($comm){
                        $data['data'][$key]['show_code'] = 8;
                        $data['data'][$key]['show_num'] = 0;
                    }else{
                        $data['data'][$key]['show_code'] = 7;
                        $data['data'][$key]['show_num'] = 0;
                    }
                    break;
            }
        }

        $return['status'] = 1;
        if($data['data']){
            $return['data'] = $data['data'];    //数据
        }else{
            $return['data'] = array();
        }
        $return['also'] = $data['also'];    //判断是否还有下一页
        $return['p']    = $_GET['p'];       //当前页
        $this->getReturn($return);
    }


    /**
     * 接受订单(定向和预约)详情
     * @param id    订单id
     * @param token 用户口令
     * @return  array   组合订单信息
     */
    public function acceptOrder()
    {
        $this->checkUser();
        $return = $this->returns();
        $id = I('request.id');    //接受订单id
        $Orders = D('orders');
        $Logi   = D('logistics');
        if(!$data = $Orders->find($id)){
            $return['message'] = '订单不存在';
            $this->getReturn($return);
        }
        //预约订单和定向订单公共信息
        $info['order_sn'] = $data['order_sn'];  //订单号
        $info['order_depart_city'] = $data['order_depart_city'];
        $info['order_des_city'] = $data['order_des_city'];
        $info['order_weight'] = $data['order_weight'];  //载重
        $info['order_bulk'] = $data['order_bulk'];      //体积
        $info['order_depart_details'] = $data['order_depart_details'];//起点详细地址
        $info['order_des_details'] = $data['order_des_details'];    //终点详细地址
        $info['order_time'] = date('Y-m-d H点', $data['order_time']);
        //货物类型
        $info['order_cargo_type'] = $data['order_cargo_type'];

        //判断是预约发货还是定向发货
        if($data['order_type'] == 1){   //预约
            //查询出收到的报价
            $map['order_id'] = $data['order_id'];
            $offData = M('offer')->where($map)->select();
            if(!$offData){
                $return['data'] = $info;
                $return['data']['offer'] = array();
                $return['status'] = 1;
                $this->getReturn($return);
            }

            //循环报价，查询出物流公司名称
            foreach($offData as $key=>$val){
                $logiData = $Logi->where('logistics_id = '.$val['logistics_id'])->getField('logistics_name');
                $offData[$key]['logistics_name'] = $logiData;
            }

            $return['data'] = $info;
            if($offData){
                $return['data']['offer'] = $offData;
            }else{
                $return['data']['offer'] = array();
            }
            $return['status'] = 1;
            $this->getReturn($return);

        }elseif($data['order_type'] == 2){  //定向
            //查询出物流公司信息
            $logiData = M('logistics')->field('logistics_name,logistics_phone,logistics_img')->find($data['logistics_id']);
            $info['logi_name']  =  $logiData['logistics_name']; //物流公司名称
            $info['logi_phone']  =  $logiData['logistics_phone'];   //物流公司电话
            $info['logi_img']  =  imgDomain($logiData['logistics_img']);   //物流公司图标

            //查询出物流公司有无对该订单报价
            $map['order_id'] = $data['order_id'];
            $map['logistics_id'] = $data['logistics_id'];
            $offData = M('offer')->where($map)->getField('offer_money');
            if(!$offData){
                $return['status'] = 1;
                $return['message'] = '对方未报价！';
                $return['data'] = $info;
                $this->getReturn($return);
            }
            $info['offer_money'] = $offData;

            //赋值
            $return['status'] = 1;
            $return['data'] = $info;
            $this->getReturn($return);
        }

    }


    /**
     * 接受预约物流报价
     * @param token
     * @param order_id 订单id
     * @param logistics_id  物流id
     * @param money     报价价格
     */
    public function MakeOffer()
    {
        $this->checkUser();
        $result = $this->returns();

        $order_id       = I('request.order_id');
        $logistics_id   = I('request.logistics_id');
        $money = I('request.money');

        $Orders = D('orders');
        $Offer = D('offer');

        $Orders->startTrans();      //开启事务
        //判断订单是否存在
        $order_bool = $Orders->find($order_id);
        if(!$order_bool){
            $result['message'] = '接受订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }

        if($order_bool['offer_id'] && $order_bool['order_status']){
            $result['message'] = '已接受报价，不可重复接受！';
            $this->getReturn($result);
        }

        $logi_bool = D('logistics')->field('logistics_id')->find($logistics_id);
        if(!$logi_bool){
            $result['message'] = '报价的物流公司不存在！';
            $result['code'] = 305;
            $this->getReturn($result);
        }

        $map['logistics_id'] = $logistics_id;
        $map['order_id'] = $order_id;
        $offData = $Offer->where($map)->find();
        if($offData['offer_money'] != $money){
            $result['message'] = '报价金额和数据库不一样';
            $this->getReturn($result);
        }
        //更改报价信息为确认
        $offData['offer_confirm'] = 1;
        $Offer->save($offData);

        //更改订单信息
        $save['order_id'] = $order_id;
        $save['offer_id'] = $offData['offer_id'];
        $save['order_status'] = 2;
        $save['logistics_id'] = $logistics_id;
        $condi = $Orders->save($save);
        if($condi === false){
            $Orders->rollback();        //回滚
            $result['message'] = '数据库订单更改失败！';
            $this->getReturn($result);
        }

        //生成消息，推送消息
        $content = "您的订单{$order_bool['order_sn']}报价成功，请尽快上货";
        $title = '订单报价成功';
        if(false === D('logimess')->InsertMess($logistics_id,$content,$title)){
            $Orders->rollback();    //回滚
            $result['message'] = '生成消息失败';
            $this->getReturn($result);
        }
        $Orders->commit();  //提交

        //推送极光
        $jpushId = D('logistics')->getJPush($logistics_id);
        $this->bbs_push_server($jpushId,$content,$title);

        //生成广播消息
        $Lname = D('logistics')->where('logistics_id ='.$logistics_id)->getField('logistics_name');
        $scontent = "{$order_bool['order_depart_city']}--{$order_bool['order_des_city']}路线，{$Lname}抢单成功";
        D('broadcast')->createSystem(1,$scontent);

        $result['message'] = '接受报价成功！';
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 接受定向报价
     */
    public function orientOffer()
    {
        $this->checkUser();
        $result = $this->returns();

        $order_id = I('request.order_id');
        $money  = I('request.money');
        $Offer = D("offer");
        $Orders = D("orders");
        $data = $Orders->find($order_id);

        if($data['offer_id']){
            $result['message'] = '该订单已接受报价，不可重复接受！';
            $this->getReturn($result);
        }
        //取报价信息
        $map['logistics_id'] = $data['logistics_id'];
        $map['order_id'] = $data['order_id'];
        $offData = $Offer->where($map)->find();

        if($offData['offer_money'] != $money){
            $result['message'] = '报价金额与数据库不一致!';
            $this->getReturn($result);
        }

        //更改报价信息为确认
        $offData['offer_confirm'] = 1;
        $Offer->save($offData);

        //更改订单信息
        $save['order_id'] = $data['order_id'];
        $save['offer_id'] = $offData['offer_id'];
        $save['order_status'] = 2;
        $condi = $Orders->save($save);
        if($condi === false){
            $result['message'] = '接受失败，请重试！';
            $this->getReturn($result);
        }

        //生成消息，推送消息
        $content = "您的订单{$data['order_sn']}报价成功，请尽快上货";
        $title = '订单报价成功';
        D('logimess')->InsertMess($data['logistics_id'],$content,$title);
        //推送极光
        $jpushId = D('logistics')->getJPush($data['logistics_id']);
        $this->bbs_push_server($jpushId,$content,$title);

        $result['message'] = '接受报价成功！';
        $result['status'] = 1;
        $this->getReturn($result);
    }



    /**
     * 确认送达（未上货和运输中）
     * @param token
     * @param id    订单id
     */
    public function affirmOrder()
    {
        $this->checkUser();
        $return = $this->returns();

        $id = I('request.id');    //接受订单id
        $Orders = D('orders');
        $Logi   = D('logistics');
        if(!$data = $Orders->find($id)){
            $return['message'] = '订单不存在';
            $this->getReturn($return);
        }

        //公共信息
        $info['order_sn'] = $data['order_sn'];  //订单号
        $wireData = D('car_wire')->getOneInfo($data['wire_id']);   //查询出路线信息
        $info['wire_state_name'] = $this->get_region_name($wireData['wire_state']);    //路线起始地名称
        $info['wire_end_name'] = $this->get_region_name($wireData['wire_end']);      //路线目的地名称

        $info['order_weight'] = $data['order_weight'];  //载重
        $info['order_bulk'] = $data['order_bulk'];      //体积
        $info['order_depart_details'] = $data['order_depart_details'];//起点详细地址
        $info['order_des_details'] = $data['order_des_details'];    //终点详细地址

        //查询出物流公司信息
        $logiData = M('logistics')->field('logistics_name,logistics_phone,logistics_img')->find($data['logistics_id']);
        $info['logi_name']      =  $logiData['logistics_name']; //物流公司名称
        $info['logi_phone']     =  $logiData['logistics_phone'];   //物流公司电话
        $info['logi_img']       =  $logiData['logistics_img'];   //物流公司图标
        //查询报价
        $map['order_id'] = $data['order_id'];
        $map['logistics_id'] = $data['logistics_id'];
        $offData = M('offer')->where($map)->getField('offer_money');
        $info['offer_money'] = $offData;

        $return['status'] = 1;
        if($data['order_is_goods'] == 1){
            $map['order_id'] = $data['order_id'];
            $map['wire_id'] = $data['wire_id'];
            $map['logistics_id'] = $data['logistics_id'];
            $trackData = M('track')->where($map)->order('track_id desc')->select();
            if(!$trackData){
                $info['track']  = '等待物流公司发布信息！';
                $return['message'] = '已上货，等待物流公司发布物流信息！';
                $return['data'] = $info;
                $this->getReturn($return);
            }
            foreach($trackData as $key=>$val){
                $trackData[$key]['track_time'] = date('Y-m-d H:i',$val['track_time']);
            }
            $info['track'] = $trackData;
            //赋值
            $return['data'] = $info;
            $this->getReturn($return);
        }else{
            $info['track'] = '物流公司尚未上货';
            $return['data'] = $info;
            $return['message'] = '物流公司尚未上货';
            $this->getReturn($return);
        }
    }


    /**
     * 已完成的订单（已评价和未评价）
     * @param token 口令
     * @param id    订单id
     */
    public function finishOrder()
    {
        $this->checkUser();
        $return = $this->returns();

        $id = I('request.id');    //接受订单id
        $Orders = D('orders');
        $Logi   = D('logistics');
        if(!$data = $Orders->find($id) ){
            $return['message'] = '订单不存在';
            $this->getReturn($return);
        }
        //公共信息
        $info['order_sn'] = $data['order_sn'];  //订单号
        $wireData = D('car_wire')->getOneInfo($data['wire_id']);   //查询出路线信息
        $info['wire_state_name'] = $this->get_region_name($wireData['wire_state']);    //路线起始地名称
        $info['wire_end_name'] = $this->get_region_name($wireData['wire_end']);      //路线目的地名称
        $info['order_weight'] = $data['order_weight'];  //载重
        $info['order_bulk'] = $data['order_bulk'];      //体积
        $info['order_depart_details'] = $data['order_depart_details'];//起点详细地址
        $info['order_des_details'] = $data['order_des_details'];    //终点详细地址

        //查询出物流公司信息
        $logiData = M('logistics')->field('logistics_name,logistics_phone,logistics_img')->find($data['logistics_id']);
        $info['logi_name']      =  $logiData['logistics_name']; //物流公司名称
        $info['logi_phone']     =  $logiData['logistics_phone'];   //物流公司电话
        $info['logi_img']       =  imgDomain($logiData['logistics_img']);   //物流公司图标
        //查询报价
        $map['order_id'] = $data['order_id'];
        $map['logistics_id'] = $data['logistics_id'];
        $offData = M('offer')->where($map)->getField('offer_money');
        $info['offer_money'] = $offData ? $offData : '';

        $map['order_id'] = $data['order_id'];
        $map['wire_id'] = $data['wire_id'];
        $map['logistics_id'] = $data['logistics_id'];
        $trackData = M('track')->where($map)->order('track_id desc')->select();

        foreach($trackData as $key=>$val){
            $trackData[$key]['track_time'] = date('Y-m-d H:i',$val['track_time']);
        }
        $info['track'] = $trackData ? $trackData : '';

        $where['order_id'] = $data['order_id'];
        $where['user_id'] = $data['user_id'];
        $commData = M('comment')->where($where)->find();
        if(!$commData){
            $return['show_code'] = 7;
            $return['data'] = $info;
            $return['status'] = 1;
            $this->getReturn($return);
        }
        //赋值
        $return['data'] = $info;
        $return['status'] = 1;
        $return['show_code'] = 8;
        $this->getReturn($return);
    }


    /**
     * 评价订单
     * @param token 用户口令
     * @param id    订单id
     * @param content 评论内容
     * @param anonym  是否匿名
     */
    public function comment()
    {
        $this->checkUser();
        $return = $this->returns();

        //接受值，订单id  评论内容  是否匿名
        $data = array();
        $data['order_id'] = I('request.id');
        $data['comment_content'] = I('request.content');
        $data['comment_anonym'] = I('request.anonym');
        $data['user_id'] = $this->user_id;  //用户id
        $data['comment_time'] = time(); //评论时间
//        if($data['comment_anonym'] != 1){   //如果该评论不是匿名，则使用用户id
//            $data['user_id'] = $this->user_id;  //用户id
//        }

        //判断订单是否存在
        $res = M('orders')->find($data['order_id']);
        if(!$res){
            $return['message'] = '订单不存在！';
            $return['code']    = 203;
            $this->getReturn($return);
        }

        $Comm = D('comment');
        //再次判断是否已经评论过
        $num = $Comm->where('order_id = '.$data['order_id'])->count();
        if($num){
            $return['message'] = '你已经评论过了，不可重复评论！';
            $return['code']    = 201;
            $this->getReturn($return);
        }

        if(!$Comm->create($data)){
            $return['message'] = $Comm->getError();
            $this->getReturn($return);
        }

        $insertID = $Comm->add($data);
        if(!$insertID){
            $return['message'] = '评论失败';
            $return['code']    = 204;
            $this->getReturn($return);
        }

        $return['status'] = 1;
        $return['message'] = '评论成功！';
        $return['code'] = 200;
        $this->getReturn($return);
    }


    /**
     * 取消订单
     * @param id    订单id
     */
    public function orderCancel()
    {
        $this->checkUser();

//        $this->bbs_push_android_server('120c83f76027552ea60','内容1','标题');
//        die;

        $result = $this->returns();

        $id = I('request.id',0);
        $Order = M('orders');
        //判断订单是否存在
        if(!$Odata = $Order->find($id)){
            $result['message'] = '需要取消的订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }

        //判断订单是否已经上货
        if($Odata['order_is_goods'] == 1){
            $result['message'] = '订单已经上货，不可取消！';
            $this->getReturn($result);
        }

        //判断是否重复取消订单
        if($Odata['order_status'] == 4){
            $result['message'] = '订单不可重复取消！';
            $this->getReturn($result);
        }

        //更改订单状态
        $Odata['order_status'] = 4;
        $as = $Order->save($Odata);
        if(false === $as){
            $result['message'] = '系统错误！';
            $this->getReturn($result);
        }

        //如果有物流抢单，则发消息给物流公司
        if($Odata['logistics_id']){
            $info['logistics_id'] = $Odata['logistics_id'];
            $info['title'] = '您的订单客户已取消';
            $info['content'] = $Odata['order_sn'].'订单客户已取消！';
            $info['time'] = time();
            M('logimess')->add($info);

            //推送
            $jpushId = D('logistics')->getJPush($Odata['logistics_id']);
            $this->bbs_push_android_server($jpushId,$info['content'],$info['title']);
        }


        $result['message'] = '成功取消订单！';
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 确认送达
     * @param token 用户口令
     * @param id    订单id
     */
    public function orderAttain()
    {
        $this->checkUser();
        $result = $this->returns();

        $id = I('request.id');
        //判断id是否有值
        if(!$id){
            $result['message'] = '需要确认送达的订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }
        $Order = D('orders');

        $data = $Order->find($id);
        //判断订单是否存在
        if(!$data){
            $result['message'] = '需要确认送达的订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }

        //判断订单是否已经确认过了
        if($data['order_user_affirm'] == 1){
            $result['message'] = '订单已确认送达！';
            $result['status'] = 1;
            $this->getReturn($result);
        }

        //改变订单状态
        if($Order->where('order_id = '.$id)->setField(array('order_user_affirm' =>1,'order_status'=>3)) === false){
            $result['message'] = '系统繁忙,请稍后重试！';
            $this->getReturn($result);
        }

        $result['message'] = '确认成功，订单已完成！';
        $result['status'] = 1;
        $this->getReturn($result);
    }
}