<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:29
 */

namespace Admin\Controller;
use Think\Controller;

class CarWireController extends CommonController
{
    public function index()
    {
        if($_POST['filter']){
            $data = json_decode($_POST['filter']);
            $regionModel = M('region');
            if(!empty($data->wire_state)){
                $item = $regionModel->where("region_name = '{$_POST['wire_state']}' AND region_type = 2")->field('region_id')->find();
                $data->wire_state = $item['region_id'];
            }
            if(!empty($data->wire_end)){
                $item = $regionModel->where("region_name = '{$_POST['wire_end']}' AND region_type = 2")->field('region_id')->find();
                $data->wire_end = $item['region_id'];
            }
            $_POST['filter'] =json_encode($data);
            return $this->ajaxList();
            $this->display($this->tpl_name);
        }
        return parent::index();
    }

    public function ajaxList()
    {
        $result = $this->getAjaxList('CarWire');
        $this->gridReturn($result['rows'], $result['total']);
    }


    /**
     * 切换路线状态
     * @return
     */
    public function toggleStatus() {
        $WireService = D('CarWire', 'Service');
        if (!isset($_POST['id'])
            || !$WireService->existLogi($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        if (!$_POST['effect']) {
            $WireService->setStatus($_POST['id'], 1);
        } else {
            $WireService->setStatus($_POST['id'], 0);
        }

        $info = $_POST['effect'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }


    public function add() {
        $CarService = D('CarWire', 'Service');
        $this->assign('pro',$CarService->getRegion(1)); //查询出所有的省
        $this->assign('city',$CarService->getRegion(2)); //查询出所有的市
        $this->display();
    }

    /**
     * 创建
     * @return
     */
    public function create() {
        if($_POST['start_city_val']){
            $data['wire_state'] = $_POST['start_city_val'];
        }else{
//            return $this->errorReturn('起始地址信息不全！');
            $this->error('起始地址信息不全！');
        }
        if ($_POST['end_city_val']) {
            $data['wire_end'] = $_POST['end_city_val'];
        }else{
//            return $this->errorReturn('目的地址信息不全！');
            $this->error('目的地址信息不全！');
        }

        $as = M('car_wire')->where($data)->find();
        if($as){
//            return $this->errorReturn('该线路已经添加了！');
            $this->error('该线路已经添加了！');
        }
        $data['gently'] = $_POST['gently'];
        $data['weighty'] = $_POST['weighty'];
        $result = D('CarWire', 'Service')->add($data);
        if (!$result) {
            $this->error('系统错误！');
        }

        $this->success('添加成功');
    }


    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('CarWire', 'Service')->existWire($_GET['id'])) {
            return $this->error('需要编辑的路线不存在！');
        }

//        $row = D('CarWire', 'Service')->getOne($_GET['id']);
        $row = D('CarWire')->getOne($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的路线不存在！');
        }

        $CarService = D('CarWire', 'Service');
        $this->assign('pro',$CarService->getRegion(1)); //查询出所有的省
        $this->assign('city',$CarService->getRegion(2)); //查询出所有的市

        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 更新节点信息
     * @return
     */
    public function update() {
        $WireService = D('CarWire', 'Service');
        if($_POST['start_city_val']){
            $data['wire_state'] = $_POST['start_city_val'];
        }else{
            $this->error('起始地址信息不全！');
        }
        if ($_POST['end_city_val']) {
            $data['wire_end'] = $_POST['end_city_val'];
        }else{
            $this->error('目的地址信息不全！');
        }

//        $as = M('car_wire')->where($data)->find();
//        if($as){
//            $this->error('该线路已经添加了！');
//        }

        if($_POST['wire_id']){
            $data['wire_id'] = $_POST['wire_id'];
        }else{
            $this->error('系统出错！');
        }
        $data['gently'] = $_POST['gently'];
        $data['weighty'] = $_POST['weighty'];
        $result = $WireService->update($data);
        if (!$result) {
            $this->error('系统出错！');
        }

        return $this->success('更新路线成功信息成功！');
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            return $this->error('您需要删除的路线不存在！');
        }

        $result = D('CarWire', 'Service')->delete($_POST['id']);
        if (false === $result) {
            return $this->error('系统错误');
        }

        return $this->success("删除路线成功！",U('CarWire/index'));
    }


    /**
     * 联动获取市区
     * @param id    省级id
     * @param array
     */
    public function getCity()
    {
        $id = I('post.id',0);

        $data = M('region')->field('region_id,region_name')->where('parent_id = '.$id)->select();
        $this->ajaxReturn($data);
    }


}