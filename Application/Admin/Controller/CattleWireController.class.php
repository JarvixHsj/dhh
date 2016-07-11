<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/5/9
 * Time: 11:29
 */

namespace Admin\Controller;
use Think\Controller;

class CattleWireController extends CommonController
{
    public function index()
    {
        return parent::index();
    }

    public function ajaxList()
    {
        $result = $this->getAjaxList('CattleWire');
        $Cattle = D('cattle');
        $Region = D('region');
        foreach($result['rows'] as $key=>$val){
            $result['rows'][$key]['name'] = $Cattle->where('id= '.$val['cattle_id'])->getField('name');
            $result['rows'][$key]['start'] = $Region->where(' = '.$val['start'])->getField('region_name');
            $result['rows'][$key]['end'] = $Region->where('region_id= '.$val['end'])->getField('region_name');
        }
        $this->gridReturn($result['rows'], $result['total']);
    }

    public function add() {
        $data = M('cattle')->field('id, name')->where('status = 1')->select();

        $CarService = D('CarWire', 'Service');
        $this->assign('pro',$CarService->getRegion(1)); //查询出所有的省
        $this->assign('city',$CarService->getRegion(2)); //查询出所有的市
        $this->assign('cattle', $data);
        $this->display();
    }

    /**
     * 创建
     * @return
     */
    public function create() {
        $data = $_POST;
        $info = array();
        if($data['id']){
            $this->update($data);
            die;
        }
        if (!$data) {
            return $this->error('参数不完整！');
        }
        if(!$data['cattle_val']){
            return $this->error('请选择黄牛！');
        }else{
            $info['cattle_id'] = $data['cattle_val'];       //黄牛id
        }
        if(!$data['start_city_val']){
            return $this->error('请选择起始地市区！');
        }else{
            $info['start'] = $data['start_city_val'];       //起始地id
        }
        if(!$data['end_pro_val']){
            return $this->error('请选择目的地省份！');
        }else{
            $info['end'] = $data['end_pro_val'];       //起始地id
        }

        $result = D('CattleWire', 'Service')->add($info);
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
            || !D('CattleWire', 'Service')->existCattleWire($_GET['id'])) {
            return $this->error('需要编辑的黄牛路线不存在！');
        }

        $row = D('CattleWire')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的黄牛路线不存在！');
        }
        $this->assign('row', $row);

        $data = M('cattle')->field('id, name')->where('status = 1')->select();
        $this->assign('cattle', $data);

        $CarService = D('CarWire', 'Service');
        $this->assign('pro',$CarService->getRegion(1)); //查询出所有的省
        $this->assign('city',$CarService->getRegion(2)); //查询出所有的市

        $this->display('add');
    }

    /**
     * 更新节点信息
     * @return
     */
    public function update($data) {
        $CattleWireService = D('CattleWire', 'Service');

        $info = array();
        $info['id'] = $data['id'];
        if (!$data) {
            return $this->error('参数不完整！');
        }
        if(!$data['cattle_val']){
            return $this->error('请选择黄牛！');
        }else{
            $info['cattle_id'] = $data['cattle_val'];       //黄牛id
        }
        if(!$data['start_city_val']){
            return $this->error('请选择起始地市区！');
        }else{
            $info['start'] = $data['start_city_val'];       //起始地id
        }
        if(!$data['end_pro_val']){
            return $this->error('请选择目的地省份！');
        }else{
            $info['end'] = $data['end_pro_val'];       //起始地id
        }

        $result = $CattleWireService->update($info);
        if (!$result) {
            $this->error('系统出错！');
        }

        return $this->success('更新信息成功！');
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            return $this->error('您需要删除的黄牛路线不存在！');
        }

        $result = D('CattleWire', 'Service')->delete($_POST['id']);
        if (false === $result) {
            return $this->error('系统错误');
        }

        return $this->success("删除黄牛路线成功！",U('CattleWire/index'));
    }


    public function person_img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'cattle');
    }

}