<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/27
 * Time: 11:31
 */

namespace Admin\Controller;
//use Think\Controller;

class VehicleController extends CommonController
{
    public function index()
    {

        return parent::index();
    }

    public function ajaxList()
    {

        $result = $this->getAjaxList('Vehicle');

        $this->gridReturn($result['rows'], $result['total']);
    }


    public function add() {
        $VehService = D('Vehicle', 'Service');

        $this->assign('data',$VehService->getLogistics());
        $this->display();
    }

    public function getAjaxList()
    {
        $res = parent::getAjaxList('Vehicle');
        $LogiService = D('logistics');
        foreach($res as $key=>$val)
        {
            foreach($val as $k=>$v)
            {
                $res[$key][$k]['logistics_name'] = $LogiService->getName($v['logistics_id']);
            }
        }
        return $res;
    }

    /**
     * 创建
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }
        if($_POST['form']['vehicle_id']){
            $result = D('Vehicle', 'Service')->update($_POST['form']);
        }else{
            $result = D('Vehicle', 'Service')->add($_POST['form']);
        }
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加成功！');
    }


    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('Vehicle', 'Service')->existVeh($_GET['id'])) {
            return $this->error('需要编辑的车辆不存在！');
        }

        $row = M('Vehicle')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的车辆不存在！');
        }
        $this->assign('data',D('Vehicle', 'Service')->getLogistics());
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 更新信息
     * @return
     */
    public function update() {
        $VehService = D('Vehicle', 'Service');
        if (!isset($_POST['form'])
            || !$VehService->existVeh($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $VehService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新车辆信息成功！');
    }


    /**
     * 删除车辆
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的车辆不存在！');
        }

        $result = D('Vehicle', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除车辆成功！");
    }



    public function img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'vehicle');
    }
}