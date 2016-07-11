<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/18
 * Time: 16:22
 */

namespace Api\Model;
use Think\Model;

class VehicleModel extends Model
{

    protected $_validate = array(
        array('logistics_id'        ,'require',     '物流公司不存在！'),
        array('vehicle_series'      ,'require',     '车辆品牌不能为空！'),
        array('vehicle_type'        ,'require',     '车辆类型不能为空！'),
        array('vehicle_licence'     ,'require',     '车牌不能为空！'),
        array('vehicle_car_weight'  ,'require',     '车辆重量不能为空！'),
        array('vehicle_weight'      ,'require',     '车载重不能为空！'),
        array('vehicle_long'        ,'require',     '车长不能为空！'),
        array('vehicle_height'      ,'require',     '车高不能为空！'),
        array('vehicle_age'         ,'require',     '车龄不能为空！'),
        array('vehicle_cargo_long'  ,'require',     '车箱长不能为空！'),
        array('vehicle_cargo_weight','require',     '车箱宽不能为空！'),
        array('vehicle_cargo_height','require',     '车箱高不能为空！'),
        array('vehicle_intro'       ,'require',     '车辆简介不能为空！'),
        array('vehicle_cargo_type'  ,'require',     '车辆简介不能为空！'),
        array('vehicle_car_img'     ,'require',     '车辆照片不能为空！'),
        array('vehicle_licence_img' ,'require',     '车牌照片不能为空！'),
        array('vehicle_year_img'    ,'require',     '车辆年审图片不能为空！'),
        array('vehicle_way_img'     ,'require',     '车辆道路运输图片不能为空！'),
    );


}