<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/2
 * Time: 15:47
 */

namespace Api\Controller;
use Think\Controller;

class WeblogisticsController extends LogisticsCommonController
{

    /**
     * 客户端订单货物跟踪
     * @param token
     * @param id    订单id
     */
    public function trackGoods()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $token = I('request.token');
        $id = I('request.id');

        //判断订单是否存在
        $data = D('orders')->find($id);
        if(empty($data)){
            $result['code'] = 304;
            $result['message'] = '订单不存在！';
            $this->getReturn($result);
        }

        //获取物流公司电话
        $logiData = D('logistics')->field('logistics_phone')->find($data['logistics_id']);

        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Order/trackLogi',"id=$id");
        $result['data']['order_status'] = $data['order_status'];
        $result['data']['phone'] = $logiData['logistics_phone'];
        $result['data']['is_goods'] = $data['order_is_goods'];
        $this->getReturn($result);
    }



    /**
     * 已完成内页
     * @param token
     * @param id    订单id
     */
    public function trackDone()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $token = I('request.token');
        $id = I('request.id');

        //判断订单是否存在
        $data = D('orders')->find($id);
        if(empty($data)){
            $result['code'] = 304;
            $result['message'] = '订单不存在！';
            $this->getReturn($result);
        }

        //获取物流公司电话
        $logiData = D('logistics')->field('logistics_phone')->find($data['logistics_id']);

        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Order/trackLogiDone',"id=$id");
        $result['data']['order_status'] = $data['order_status'];
        $result['data']['phone'] = $logiData['logistics_phone'];
        $this->getReturn($result);
    }


    /**
     * 消息中心--消息详情
     */
    public function messageDetails()
    {
        $result = $this->returns();
        $id = I('request.id');

        $Mess = M('logimess');
        if(!$id){
            $result['message'] = '信息不存在！';
            $this->getReturn($result);
        }
        if(!$Mess->find($id)){
            $result['message'] = '信息不存在！';
            $this->getReturn($result);
        }

        //更改未读状态
        $map['id'] = $id;
        $map['read'] = 1;
        $as = $Mess->save($map);
        if(false === $as){
            $result['message'] = '系统错误，请重试！';
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Message/logisticsMess',"id=$id");
        $this->getReturn($result);
    }
}