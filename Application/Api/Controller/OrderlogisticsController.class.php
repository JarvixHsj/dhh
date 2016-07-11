<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/21
 * Time: 11:20
 */

namespace Api\Controller;
use Think\Controller;

/**
 * Class OrderlogisticsController
 * @package Api\Controller
 * 物流端订单表
 */
class OrderlogisticsController extends LogisticsCommonController
{

    /**
     * 物流端个人中心-我的订单 未确定订单
     */
    public function uncerOrder()
    {
//        检查是否登录，如果登录 this->logi_id  则有用户id
        $this->checkLogistics();
        $result = $this->returns();
        $lid = $this->logi_id;

        $_GET['p'] = I('request.p', 1);    //请求数据页数
        $search = I('request.search');      //搜索订单号
        $Orders = D('orders');
        $Offer = D('offer');
        $Region = D('region');
        $Car_wire = D('car_wire');
        $Users = D('users');
        $page_size = 3;
        $status = I('request.status');  //接受查看订单的状态

        switch ($status) {
            //1=未确定订单，说明是未确定订单，就查看是否有报价
            case 1:

                $expiration = time()-C('EXPIRATION_TIME')*60;
                $count = $Orders->query("SELECT COUNT(*) as tp_count FROM dhh_orders o WHERE ( o.order_status = 1 ) AND  (o.order_sn like '%".$search."%') AND ( o.logistics_id = '" . $lid . "' ) AND (( (o.order_type = 2)  AND (o.order_is_expiration = 0) AND (o.order_is_expiration = 0) AND ( o.order_add_time <= '".$expiration."') ) OR ( ( o.order_type = 1 ) ))");
                $count = $count[0]['tp_count'];

                $Page = new \Think\Page($count, $page_size);// 传入总记录数和每页显示的记录数(25)
                $sql = "SELECT o.* FROM dhh_orders o WHERE ( o.order_status = 1 ) AND (o.order_sn like '%".$search."%') AND ( o.logistics_id = '" . $lid . "' ) AND ( ((o.order_type = 2)  AND (o.order_is_expiration = 0) AND  ( o.order_add_time >= '".$expiration."') ) OR ( ( o.order_type = 1 ) )) LIMIT " . $Page->firstRow . ',' . $Page->listRows;
                $data = $Orders->query($sql);

                //判断是否有值
                if (!$data) {
                    $result['message'] = '没有数据了';
                    $result['data'] = array();
                    $result['code'] = 404;
                    $result['status'] = 1;
                    $this->getReturn($result);
                }
                $info = array();
                foreach ($data as $key => $val) {   //循环

                    if ($val['order_type'] == 1) {      //预约状态
                        $item = array();
                        $item['order_id'] = $val['order_id'];   //id
                        $item['order_sn'] = $val['order_sn'];   //订单号
                        $item['order_type'] = $val['order_type'];   //订单类型
                        $item['order_status'] = $val['order_status'];   //订单状态
                        $item['order_time'] = date('m-d', $val['order_time']);  //订单发货时间
                        $item['order_depart_city'] = $val['order_depart_city'];   //订单起始地城市
                        $item['order_des_city'] = $val['order_des_city'];     //订单目的地城市

                        if ($offMoney = $Offer->getIfMoney($lid)) {      //有没有报价判断
                            $item['offer_money'] = '￥' . $offMoney['offer_money'];
                            $item['offer_if'] = 1;
                        } else {
                            $item['offer_money'] = '';
                            $item['offer_if'] = 0;
                        }

                        $info[] = $item;    //赋值保存

                    } elseif ($val['order_type'] == 2) {
                        $carData = $Car_wire->field('wire_state,wire_end')->find($val['wire_id']);

                        $item['order_id'] = $val['order_id'];
                        $item['order_sn'] = $val['order_sn'];

                        $item['order_type'] = $val['order_type'];
                        $item['order_status'] = $val['order_status'];
                        $item['order_time'] = date('m-d', $val['order_time']);

                        $item['order_depart_city'] = $val['order_depart_city'];   //订单起始地城市
                        $item['order_des_city'] = $val['order_des_city'];     //订单目的地城市

                        if ($offMoney = $Offer->getIfMoney($lid)) {      //有没有报价判断
                            $item['offer_money'] = '￥' . $offMoney['offer_money'];
                            $item['offer_if'] = 1;
                        } else {
                            $item['offer_money'] = '';
                            $item['offer_if'] = 0;
                        }

                        $info[] = $item;
                    }
                }

                if ($info == null) {
                    $result['data'] = array();
                    $result['code'] = 404;
                } else {
                    $result['data'] = $info;
                }
                //判断是否还有下一页
                if ($_GET['p'] >= ceil($count / $page_size)) {
                    $result['also'] = 0;
                } else {
                    $result['also'] = 1;
                }
                $result['status'] = 1;
                $this->getReturn($result);
                break;

            //2=未完成订单
            case 2:
                //如果为1，表示已上货正在运输中，0表示未上货
                $count = $Orders->where('order_status = 2 AND logistics_id = ' . $this->logi_id . " AND order_sn like '%".$search."%'")->count();
                $Page = new \Think\Page($count, $page_size);
                $data = $Orders->where('order_status = 2 AND logistics_id = ' . $this->logi_id . " AND order_sn like '%".$search."%'")->limit($Page->firstRow . ',' . $Page->listRows)->select();
                //根据不同订单类型 用不同的方式获取订单信息
                foreach ($data as $key => $val) {
                    $userData = $Users->field('user_name,user_img')->find($val['user_id']);
                    $info['user_name'] = $userData['user_name'];
                    $info['user_img'] = imgDomain($userData['user_img']);
                    $info['order_id'] = $val['order_id'];
                    $info['order_sn'] = $val['order_sn'];
                    $info['order_time'] = date('m-d', $val['order_time']);
                    $info['order_is_goods'] = $val['order_is_goods'];
                    $item[] = $info;
                }
                if ($item == null) {
                    $item = array();
                    $result['code'] = 404;
                }
                $result['status'] = 1;
                $result['data'] = $item;
                //判断是否还有下一页
                if ($_GET['p'] >= ceil($count / $page_size)) {
                    $result['also'] = 0;
                } else {
                    $result['also'] = 1;
                }
                $this->getReturn($result);
                break;
            //3=已完成
            case 3:
                //查看评论表是否有该用户对该订单的评论
                $count = $Orders->where('order_status = 3 AND logistics_id = ' . $this->logi_id . " AND order_sn like '%".$search."%'")->count();
                $Page = new \Think\Page($count, $page_size);
                $data = $Orders->where('order_status = 3 AND logistics_id = ' . $this->logi_id . " AND order_sn like '%".$search."%'")->limit($Page->firstRow . ',' . $Page->listRows)->select();

                $CommentModel = D('Comment');
                foreach ($data as $key => $val) {
                    $userData = $Users->field('user_name,user_img')->find($val['user_id']);
                    $info['user_name'] = $userData['user_name'];
                    $info['user_img'] = imgDomain($userData['user_img']);
                    $info['order_id'] = $val['order_id'];
                    $info['order_sn'] = $val['order_sn'];
                    $info['order_time'] = date('m-d', $val['order_time']);
                    $info['is_comment'] = $CommentModel->existEmpty($val['order_id']);      //判断是否有评论
                    $item[] = $info;
                }
                if ($item == null) {
                    $item = array();
                    $result['code'] = 404;
                }
                $result['status'] = 1;
                $result['data'] = $item;
                //判断是否还有下一页
                if ($_GET['p'] >= ceil($count / $page_size)) {
                    $result['also'] = 0;
                } else {
                    $result['also'] = 1;
                }
                $this->getReturn($result);
                break;
        }

    }


    /**
     * 物流端确认上货
     * @param id 订单id
     * @param token
     * @return boolean
     */
    public function affirmGoods()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();
        $Orders = D('orders');

        $id = I('request.id');  //订单id
        $bool = $Orders->find($id);
        if (!$bool) {
            $result['message'] = '订单不存在，不可操作！';
            $result['code'] = 304;
            $this->getReturn($result);
        }

        $update['order_is_goods'] = 1;
        $updata['order_status'] = 2;
        $update['order_id'] = $id;
        $res = $Orders->save($update);
        if ($res === false) {
            $result['message'] = '未提交成功！';
            $this->getReturn($result);
        }

        //生成消息，推送消息
        $content = "您的订单{$bool['order_sn']}已上货";
        $title = '您有条订单状态发生改变';
        $as = D('message')->InsertMess($bool['user_id'],$content,$title);
        //推送极光
        $jpushId = D('users')->getJPush($bool['user_id']);
        $this->bbs_push_client($jpushId,$content,$title);

        $result['status'] = 1;
        $result['message'] = '完成上货！';
        $this->getReturn($result);
    }


    /**
     * 物流端预约订单内容页
     * @param token
     * @param id //订单id
     */
    public function OrderDetails()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $id = I('request.id');  //订单id;

        $data = M('orders')->find($id);
        if (!$data) { //如果订单信息不存在则返回报错
            $result['message'] = '订单不存在';
            $result['code'] = 304;
            $this->getReturn($result);
        }
        //获取订单用户名
        $userData = D('users')->field('user_name,user_img')->find($data['user_id']);

        $Select = D('select');
        $data['order_time'] = date('Y-m-d H点', $data['order_time']);
        //查询报价表，物流公司是否有报价过
        $map['logistics_id'] = $this->logi_id;
        $map['order_id'] = $data['order_id'];
        $offData = M('offer')->field('offer_id,offer_money')->where($map)->find();
        if ($offData) {    //判断是否报价
            $data['money'] = $offData['offer_money'];
            $data['money_if'] = 1;
        } else {
            $data['money'] = '';
            $data['money_if'] = 0;
            //如果你未报价，查询出诚信保证金
            $money_data = $Select->field('select_area_state')->where('type_id = 4')->select();
            foreach ($money_data as $m => $n) {
                $data['money_data'][] = $n['select_area_state'];
            }
        }
        $data['user_name'] = $userData['user_name'];
        $data['user_img'] = imgDomain($userData['user_img']);

        $result['message'] = '查询成功！';
        $result['status'] = 1;
        $result['data'] = $data;
        $this->getReturn($result);
    }



    /**
     * 未确认订单--支付
     * @param id //订单id
     * @param token
     */
    public function indeterminatePay()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $id = I('request.id');  //订单id

        $map['order_id'] = $id;
        $map['logistics_id'] = $this->logi_id;
        $offData = D('offer')->field('offer_id,offer_money')->where($map)->find();

        if (!$offData) {
            $result['message'] = '订单报价不存在！';
            $result['code'] = 306;
            $result['data'] = (object)array();
            $this->getReturn($result);
        }

        $result['data'] = $offData;
        $result['status'] = 1;
        $result['message'] = '返回成功！';
        $this->getReturn($result);
    }


    /**
     * 未完成内容页
     * @param id 订单id
     */
    public function undoneOrderDetails()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $Region = D('region');
        $id = I('request.id');

        $data = M('orders')->find($id);//查询出订单
        if ($data == null) {
            $result['messge'] = '订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }

        $data['user_name'] = D('users')->getName($data['user_id']);  //获取订单用户名

        //查询路线，根据发货类型使用不同的方式查询
        if ($data['order_type'] == 1) {   //预约
            $data['wire_state'] = $Region->getName($data['order_depart_state']);  //起始地名称
            $data['wire_end'] = $Region->getName($data['order_des_end']);    //目的地名称
        } elseif ($data['order_type'] == 2) {  //定向
            $wireData = M('car_wire')->find($data['wire_id']);
            $data['wire_state'] = $Region->getName($wireData['wire_state']);  //起始地名称
            $data['wire_end'] = $Region->getName($wireData['wire_end']);    //目的地名称
        }
        $data['order_weight'] = $data['order_weight'] . 't';
        $data['order_bulk'] = $data['order_bulk'] . 'm³';
        $data['order_cargo_name'] = D('select')->getValue($data['order_cargo_type']);
        $data['order_time'] = date('m-d', $data['order_time']);

        //查询报价
        $offData = M('offer')->field('offer_money')->find($data['offer_id']);
        $data['money'] = $offData['offer_money'];

        //查询物流信息
        $where['order_id'] = $id;
        $where['logistics_id'] = $this->logi_id;
        $trackData = M('track')->where($where)->order('track_time')->select();
        foreach ($trackData as $k => $v) {
            $trackData[$k]['track_time'] = date('Y-m-d H:i', $v['track_time']);
        }

        //物流信息
        if ($trackData == null) {
            $data['track'] = (object)array();
        } else {
            $data['track'] = $trackData;
        }

        $result['data'] = $data;
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 取消订单
     * @param id    订单id
     */
    public function orderCancel()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();

        $id = I('request.id', 0);
        $Order = M('orders');
        //判断订单是否存在
        if (!$Odata = $Order->find($id)) {
            $result['message'] = '需要取消的订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }
        //判断订单是否已经上货
        if ($Odata['order_is_goods'] == 1) {
            $result['message'] = '订单已经上货，不可取消！';
            $this->getReturn($result);
        }

        //判断是否重复取消订单
        if ($Odata['order_status'] == 4) {
            $result['message'] = '订单不可重复取消！';
            $this->getReturn($result);
        }

        //更改订单状态
        $Odata['order_status'] = 4;
        $as = $Order->save($Odata);
        if (false === $as) {
            $result['message'] = '系统错误！';
            $this->getReturn($result);
        }

//        $info['user_id'] = $Odata['user_id'];
//        $info['message_content'] = $Odata['order_sn'] . '订单物流已取消！';
//        $info['message_time'] = time();
//        M('message')->add($info);
        //生成消息，推送消息
        $content = "您的订单{$Odata['order_sn']}已取消订单";
        $title = '您有条订单状态发生改变';
        $as = D('message')->InsertMess($Odata['user_id'],$content,$title);
        //推送极光
        $jpushId = D('users')->getJPush($Odata['user_id']);
        $this->bbs_push_client($jpushId,$content,$title);

        $result['message'] = '成功取消订单！';
        $result['status'] = 1;
        $this->getReturn($result);

    }


    /**
     * 添加物流信息
     * @param token     物流口令
     * @param $site     当前地址
     * @param $id       订单id
     * @param $content  当前物流信息描述
     */
    public function addTrack()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();

        $Track = D('track');
        $info['order_id']       = I('request.id');
        $info['logistics_id']   = $this->logi_id;
        $info['track_name']     = '【'.I('request.site').'】';
        $info['track_content']  = I('request.content');
        $info['track_time'] = time();
        if(!$Track->create($info)){
            $result['message'] = $Track->getError();
            $this->getReturn($result);
        }
        $as = $Track->add($info);
        if(!$as){
            $result['message'] = '系统错误！';
            $result['sql'] = $Track->getLastSql();
            $this->getReturn($result);
        }

        $result['message'] = '添加成功！';
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 物流端确认送达
     * @param $id  订单id
     * @param token 口令
     *
     */
    public function orderAttain()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();

        $id = I('request.id');
        //判断id是否有值
        if(!$id){
            $result['message'] = '需要确认送达的订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }

        $Order = D("orders");

        $data = $Order->find($id);
        //判断订单是否存在
        if(!$data){
            $result['message'] = '需要确认送达的订单不存在！';
            $result['code'] = 304;
            $this->getReturn($result);
        }
        //判断是否重复确认送达
        if($data['order_affirm'] == 1){
            $result['message'] = '不可重复确认送达！';
            $result['status'] = 1;
            $this->getReturn($result);
        }

        //开启事务
        $Order->startTrans();

        //如果未送达，则改变订单状态
        $info['order_affirm'] = 1;
        $info['order_overdue'] = time();//当前确认送达时间

        $saveOrder = $Order->where('order_id = '.$id)->setField($info);

        //发送消息给订单用户
        $mess['user_id'] = $data['user_id'];
        $mess['message_title'] = '订单已确认送达';
        $mess['message_content'] = '您的订单'.$data['order_sn'].'物流公司已确认送达！如有问题请在7天内联系物流公司洽谈，否则7天后订单则自动完成！';
        $mess['message_time'] = time();
        $addMess = M('message')->add($mess);

        if(false === $saveOrder || false == $addMess){
            $Order->rollback(); //回滚
            $result['message'] = '系统繁忙，请稍后重试！';
            $this->getReturn($result);
        }
        $Order->commit();   //确认

        //推送极光
        $jpushId = D('users')->getJPush($data['user_id']);
        $this->bbs_push_client($jpushId,$mess['message_content'],'您有一条订单状态发生改变');

        $result['message'] = '确认成功！等待客户确认送达！';
        $result['status'] = 1;
        $this->getReturn($result);
    }
}














