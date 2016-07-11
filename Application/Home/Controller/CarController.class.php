<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/19
 * Time: 11:55
 */

namespace Home\Controller;
use Think\Controller;

class CarController extends CommonController
{

    /**
     * 车辆详情页
     * @param  id   车辆id
     */
    public function carDetails()
    {
        $id = I('request.id');

        $Veh = D('vehicle');

        $data = $Veh->find($id);
        $this->assign('data',$data);
        $this->display('dhh_car_details');
    }
}