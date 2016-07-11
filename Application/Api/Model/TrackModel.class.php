<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/24
 * Time: 11:21
 */

namespace Api\Model;
use Think\Model;

class TrackModel extends Model
{

    protected $_validate = array(

        array('order_id','require','订单不能为空！'),

        array('logistics_id','require','物流公司不能为空！'),

        array('track_name','require','当前地区不能为空！'),

        array('track_content','require','当前的物流情况不能为空！')

    );

    protected $_auto = array(
        array('track_time','time','1','function')
    );
}