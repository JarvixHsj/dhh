<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/2
 * Time: 17:09
 */

namespace Api\Controller;


class DeletelogisticsController extends LogisticsCommonController
{


    /**
     * 删除路线
     * @param $id 路线关系id
     */
    public function delWirerelation()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $id = I('request.id');
        $Model = M('wire_relation');
        $data = $Model->find($id);
        if(!$data){
            $result['message'] = '该路线不存在！请重试！';
            $this->getReturn($result);
        }

        $combo = $Model->delete($id);
        if(false === $combo){
            $result['message'] = '系统错误，请重试！';
            $this->getReturn($result);
        }

        $result['message'] = '删除成功！';
        $result['status'] = 1;
        $this->getReturn($result);
    }

    /**
     * 删除车辆
     * @param $id 车辆id
     */
    public function deleteVehicle()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $id = I('request.id');
        $Model = D('vehicle');

        $as = $Model->find($id);
        if(!$as){
            $result['message'] = '该车辆已不存在！请重试！';
            $this->getReturn($result);
        }

        if(false === $Model->delete($id)){
            $result['message'] = '系统错误，请重试！';
            $this->getReturn($result);
        }

        $result['message'] = '删除成功!';
        $result['status'] = 1;
        $this->getReturn($result);

    }




}