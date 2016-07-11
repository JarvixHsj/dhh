<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/21
 * Time: 11:02
 */

namespace Api\Model;
use Think\Model;

class OfferModel extends Model
{

    public function getMoney($logiId){
        $map['logistics_id'] = $logiId;
        $map['offer_confirm'] = 0;
        if($res = $this->where($map)->find()){
            return $res;
        }else{
            return false;
        }
    }

    public function getIFMoney($logiId, $orderId){
        $map['logistics_id'] = $logiId;
        $map['order_id'] = $orderId;
        $map['offer_confirm'] = 0;
        if($res = $this->where($map)->find()){
            return $res;
        }else{
            return false;
        }
    }


    public function getOffer($logiId)
    {
        $arr = $this->where("logistics_id = {$logiId} AND offer_confirm = 0")->field('order_id')->select();
        if(is_array($arr)){
            $item = array();
            foreach($arr as $key => $val) {
                $item['order_id'][] = $val['order_id'];
            }
            return $item;
        }
        return false;
    }
}