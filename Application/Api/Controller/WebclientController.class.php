<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/2
 * Time: 11:43
 */

namespace Api\Controller;
use Think\Controller;

/**
 * Class WebController
 * @package Api\Controller
 * WEB网页专用接口.
 */
class WebclientController extends ClientCommonController
{

    /**
     * 客户端订单货物跟踪(未完成）
     * @param token
     * @param id    订单id
     */
    public function trackGoodsUndone()
    {
        $this->checkUser();
        $result = $this->returns();

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
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Order/trackClient',"id=$id");
        $result['data']['order_status'] = $data['order_status'];
        $result['data']['is_goods'] = $data['order_is_goods'];
        $result['data']['phone'] = $logiData['logistics_phone'];
        $this->getReturn($result);
    }



    /**
     * 查看订单详情（已完成）
     * @param token
     * @param id    订单id
     */
    public function trackGoodsDone()
    {
        $this->checkUser();
        $result = $this->returns();

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

        //判断订单是否评论过
        $condi['order_id'] = $id;
        $condi['logistics_id'] = $data['logistics_id'];
        $combo = M('comment')->where($condi)->find();
        if($combo){
            $result['data']['if_comm'] = 1;
        }else{
            $result['data']['if_comm'] = 0;
        }

        //赋值
        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Order/trackClientDone',"id=$id");
        $result['data']['order_status'] = $data['order_status'];
        $result['data']['phone'] = $logiData['logistics_phone'];
        $this->getReturn($result);
    }


    /**
     * 客户端查看车辆详情
     */
    public function carDetails()
    {
        $result = $this->returns();

        $id = I('request.id');  //车辆id

        $data = D("vehicle")->find($id);
        if(empty($data)){
            $result['message'] = '车辆不存在！';
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Car/carDetails',"id=$id");
        $this->getReturn($result);
    }


    /**
     * 消息中心--消息详情
     */
    public function messageDetails()
    {
        $result = $this->returns();
        $id = I('request.id');
        $Mess = M('message');
        if(!$id){
            $result['message'] = '信息不存在！';
            $this->getReturn($result);
        }
        if(!$Mess->find($id)){
            $result['message'] = '信息不存在！';
            $this->getReturn($result);
        }

        //更改未读状态
        $map['message_read'] = 1;
        $map['message_id'] = $id;
        $as = $Mess->save($map);
        if($as === false){
            $result['message'] = '系统错误，请重试！';
            $this->getReturn($result);
        }

        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Message/clientMess',"id=$id");
        $this->getReturn($result);
    }

}