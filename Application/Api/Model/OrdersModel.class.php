<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/15
 * Time: 16:03
 */

namespace Api\Model;
use Think\Model;

class OrdersModel extends Model
{
    protected $_validate = array(
        array('user_id','require','发货用户不能为空！'),
        array('offer_id','require','报价必须存在！'),
        array('order_time','require','发货时间不能为空！'),
        array('order_depart_province','require','发货地省份不能为空！'),
        array('order_depart_city','require','发货地城市不能为空！'),
        array('order_depart_details','require','发货详细地址不能为空！'),
        array('order_des_province','require','目的地省份不能为空！'),
        array('order_des_city','require','目的地城市不能为空！'),
        array('order_des_details','require','目的详细地址不能为空！'),
        array('order_cargo_type','require','货物类型不能为空！'),
        array('order_weight','require','货物重量不能为空！'),
        array('order_bulk','require','货物体积不能为空！'),
        array('order_user_phone','require','联系电话不能为空！')
    );



    /**
     * 统计路线订单次数
     * @param $id   路线id
     * @return int  订单数量
     */
    public function getCountWireNum($id)
    {
        return $this->where('wire_id = '.$id)->count();
    }


    /**
     * 获取指定状态订单
     * @param  status   订单状态
     */
    public function getOrderList($status,$user_id,$page,$search)
    {
        $data = array('also'=>1, 'data' => '');
        $where['order_status'] = $status;
        $where['user_id'] = $user_id;
        $map['_complex'] = $where;
        $map['order_sn'] = array('like',"%$search%");

        $count      = $this->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,$page);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $list = $this->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('order_add_time desc')->select();
        $data['data'] = $list;

        //计算还有没有下一页
        $totalPage = ceil($count/$page);
        if($_GET['p'] >= $totalPage){
            $data['also'] = 0;
        }
        return $data;
    }


    /**
     * 判断订单是否存在
     * @param id    订单id
     * @return boolean
     */
    public function emptyId($id)
    {
        $res = $this->field('order_id')->find($id);
        if($res){
            return true;
        }else{
            return false;
        }
    }


    /**
     * 获取订单号
     * @param id    订单id
     */
    public function orderSn($id)
    {
        $res = $this->field('order_sn')->find($id);
        return $res['order_sn'];
    }


    /**
     * 更改订单逾期状态
     */
    public function changeExpiration($id)
    {
        $this->where('order_id = '.$id)->setInc('order_is_expiration');
    }
}