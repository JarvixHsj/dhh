<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/1
 * Time: 14:22
 */

namespace Home\Controller;
use Think\Controller;

class OrderController extends CommonController
{

    /**
     * 客户端货物跟踪（未完成）
     * @param id    订单id
     */
    public function trackClient()
    {
        $id = I('request.id');  //订单id;
        $data = M('orders')->find($id);

        //获取订单用户名
        $userData = D('users')->field('user_name,user_img')->find($data['user_id']);
        $Region = D('region');
        $logiData = D('logistics')->field('logistics_name,logistics_img,logistics_phone')->find($data['logistics_id']);
        $Select = D('select');

        $data['order_weight'] = $data['order_weight'].'t';
        $data['order_bulk'] = $data['order_bulk'].'m³';
        $data['order_cargo_name'] = $Select->getValue($data['order_cargo_type']);
        $data['order_time'] = date('m-d',$data['order_time']);
        $data['order_add_time'] = date('m-d',$data['order_add_time']);
        //查询报价表，物流公司是否有报价过
        $map['order_id'] = $data['order_id'];
        $map['order_confirm'] = 1;
        $offData = M('offer')->field('offer_id,offer_money')->where($map)->find();

        //查询物流信息
        $where['order_id'] = $id;
        $trackData = M('track')->field('track_name,track_time,track_content')->where($where)->select();
        foreach($trackData as $key=>$val)
        {
            $trackData[$key]['track_time'] = date('Y-m-d H:i',$val['track_time']);
        }
        $data['track'] = $trackData;
        $data['trackcount'] = count($trackData) - 1;
        $data['money'] = $offData['offer_money'];
        $data['user_name'] = $userData['user_name'];
        $data['user_img'] = $userData['user_img'];
        $data['logistics_name'] = $logiData['logistics_name'];
        if(!$logiData['logistics_img']){
            $data['logistics_img'] = "/Home/img/nopic.jpg";
        }else{
            $data['logistics_img'] = $logiData['logistics_img'];
        }
        $data['logistics_phone'] = $logiData['logistics_phone'];
        $this->assign('data',$data);
        $this->display('dhh_goods_track');
    }



    /**
     * 客户端货物跟踪（已完成）
     * @param id    订单id
     */
    public function trackClientDone()
    {
        $id = I('request.id');  //订单id;
        $data = M('orders')->find($id);

        //获取订单用户名
        $userData = D('users')->field('user_name,user_img')->find($data['user_id']);
        $data['user_name'] = $userData['user_name'];
        $data['user_img'] = $userData['user_img'];

        $Region = D('region');
        $logiData = D('logistics')->field('logistics_name,logistics_img,logistics_phone')->find($data['logistics_id']);
        $Select = D('select');

        //赋值
        $data['order_weight'] = $data['order_weight'].'t';
        $data['order_bulk'] = $data['order_bulk'].'m³';
        $data['order_cargo_name'] = $Select->getValue($data['order_cargo_type']);
        $data['order_time'] = date('m-d',$data['order_time']);
        $data['order_add_time'] = date('m-d',$data['order_add_time']);

        //查询报价表，物流公司是否有报价过
        $map['order_id'] = $data['order_id'];
        $map['order_confirm'] = 1;
        $offData = M('offer')->field('offer_id,offer_money')->where($map)->find();
        $data['money'] = $offData['offer_money'];

        //查询物流信息
        $where['order_id'] = $id;
        $trackData = M('track')->field('track_name,track_time,track_content')->where($where)->select();
        foreach($trackData as $key=>$val)
        {
            $trackData[$key]['track_time'] = date('Y-m-d H:i',$val['track_time']);
        }
        $data['track'] = $trackData;
        $data['trackcount'] = count($trackData) - 1;
        $data['logistics_name'] = $logiData['logistics_name'];
        if(!$logiData['logistics_img']){
            $data['logistics_img'] = "/Home/img/nopic.jpg";
        }else{
            $data['logistics_img'] = $logiData['logistics_img'];
        }
        $data['logistics_phone'] = $logiData['logistics_phone'];

        //判断订单是否评论过
        $condi['order_id'] = $id;
        $condi['logistics_id'] = $data['logistics_id'];
        $combo = M('comment')->where($condi)->find();
        if($combo){
            $data['if_comm'] = 1;
        }else{
            $data['if_comm'] = 0;
        }

        $this->assign('data',$data);
        $this->display('dhh_goods_track_done');
    }



    /**
     * 物流端货物跟踪
     * @param id    订单id
     */
    public function trackLogi()
    {
        $id = I('request.id');  //订单id;
        $data = M('orders')->find($id);
//        if(!$data){ //如果订单信息不存在则返回报错
//        }

        //获取订单用户名
        $userData = D('users')->field('user_name,user_img')->find($data['user_id']);
        $Region = D('region');
        $logiData = D('logistics')->field('logistics_name,logistics_img,logistics_phone')->find($data['logistics_id']);
        $Select = D('select');

        $data['order_weight'] = $data['order_weight'].'t';
        $data['order_bulk'] = $data['order_bulk'].'m³';
        $data['order_time'] = date('m-d',$data['order_time']);
        $data['order_add_time'] = date('m-d',$data['order_add_time']);
        //查询报价表，物流公司是否有报价过
        $map['order_id'] = $data['order_id'];
        $map['order_confirm'] = 1;
        $offData = M('offer')->field('offer_id,offer_money')->where($map)->find();

        //查询物流信息
        $where['order_id'] = $id;
        $trackData = M('track')->field('track_name,track_time,track_content')->where($where)->select();
        foreach($trackData as $key=>$val)
        {
            $trackData[$key]['track_time'] = date('Y-m-d H:i',$val['track_time']);
        }
        $data['track'] = $trackData;
        $data['trackcount'] = count($trackData) - 1;    //判断高亮

        $data['money'] = $offData['offer_money'];
        $data['user_name'] = $userData['user_name'];
        $data['user_img'] = $userData['user_img'];
        if(!$userData['user_img']){
            $data['user_img'] = "/Home/img/nopic.jpg";
        }else{
            $data['user_img'] = $userData['user_img'];
        }
        $data['logistics_name'] = $logiData['logistics_name'];
        $data['logistics_img'] = $logiData['logistics_img'];
        $data['logistics_phone'] = $logiData['logistics_phone'];

        $this->assign('data',$data);
        $this->display('dhh_goods_track_logi');
    }



    /**
     * 已完成内页
     * @param id    订单id
     */
    public function trackLogiDone()
    {
        $id = I('request.id');  //订单id;
        $data = M('orders')->find($id);

        //获取订单用户名
        $userData = D('users')->field('user_name,user_img')->find($data['user_id']);
        $Region = D('region');
        $logiData = D('logistics')->field('logistics_name,logistics_img,logistics_phone')->find($data['logistics_id']);
        $Select = D('select');

        $data['order_weight'] = $data['order_weight'].'t';
        $data['order_bulk'] = $data['order_bulk'].'m³';
        $data['order_time'] = date('m-d',$data['order_time']);
        $data['order_add_time'] = date('m-d',$data['order_add_time']);

        //查询报价表，物流公司是否有报价过
        $map['order_id'] = $data['order_id'];
        $map['order_confirm'] = 1;
        $offData = M('offer')->field('offer_id,offer_money')->where($map)->find();

        $data['money'] = $offData['offer_money'];
        $data['user_name'] = $userData['user_name'];
        $data['user_img'] = $userData['user_img'];
        if(!$userData['user_img']){
            $data['user_img'] = "/Home/img/nopic.jpg";
        }else{
            $data['user_img'] = $userData['user_img'];
        }
        $data['logistics_name'] = $logiData['logistics_name'];
        $data['logistics_img'] = $logiData['logistics_img'];
        $data['logistics_phone'] = $logiData['logistics_phone'];

        //查询用户是否评价
        $data['comment'] = M('comment')->field('comment_content')->where('order_id = '.$id)->getField('comment_content');
        $this->assign('data',$data);
        $this->display('dhh_goods_track_logi_done');
    }
}