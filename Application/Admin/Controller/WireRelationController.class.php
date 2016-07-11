<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:29
 */

namespace Admin\Controller;
use Think\Controller;

class WireRelationController extends CommonController
{
    public function index()
    {
        if($_POST['filter']){
            return $this->ajaxList();
            $this->display($this->tpl_name);
        }
        return parent::index();
    }

    public function ajaxList()
    {
        $result = $this->getAjaxList('WireRelation','','rela_id desc');
        $this->gridReturn($result['rows'], $result['total']);
    }


    public function add() {
        $CarService = D('CarWire', 'Service');
        $LogiService = D('Logistics','Service');

        $this->assign('logi',$LogiService->getAll());   //查询所有物流
        $this->assign('pro',$CarService->getRegion(1)); //查询出所有的省
        $this->assign('city',$CarService->getRegion(2)); //查询出所有的市
        $this->display();
    }


    /**
     * 创建
     * @return
     */
    public function create() {

        //判断是否选择地址
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
        //判断是否填写价格和花费时间
        if($_POST['price']){
            $info['price'] = $_POST['price'];
        }else{
            $this->error('请填写重货价格');
        }
        if($_POST['predict_time']){
            $info['predict_time'] = $_POST['predict_time'];
        }else{
            $this->error('请填写花费时间');
        }
        if($_POST['gently']){
            $info['gently'] = $_POST['gently'];
        }else{
            $this->error('请填写轻货价格');
        }

        //判断路线是否存在
        $CarWire = M('car_wire');
        $as = $CarWire->where($data)->find();
        if(!$as){
            $wire_id = $CarWire->add($data);
            if(false === $wire_id){
                $this->error('新增失败，请重试！');
            }
        }else{
            $wire_id = $as['wire_id'];
        }


        $info['logistics_id'] = $_POST['logistics_id'];
        $info['wire_id'] = $wire_id;

        $relationAS = M('wire_relation')->add($info);
        if($relationAS === false){
            $this->error('新增失败，请重试！');
        }
        $this->success('添加成功');
    }


    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('WireRelation', 'Service')->existWire($_GET['id'])) {
            return $this->error('需要编辑的路线不存在！');
        }
//        $row = D('CarWire', 'Service')->getOne($_GET['id']);
        $row = D('WireRelation')->where('rela_id = '.$_GET['id'])->find();
        if (empty($row)) {
            $this->error('您需要编辑的路线不存在！');
        }

        $CarService = D('CarWire', 'Service');
        $this->assign('pro',$CarService->getRegion(1)); //查询出所有的省
        $this->assign('city',$CarService->getRegion(2)); //查询出所有的市

        $name = D("logistics")->getName($row['logistics_id']);
        $this->assign('name',$name);

        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 更新节点信息
     * @return
     */
    public function update() {

        //判断是否有传过id过来
        if($_POST['rela_id']){
            $info['rela_id'] = $_POST['rela_id'];
        }else{
            $this->error('无效的操作');
        }
        //判断是否选择地址
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
        //判断是否填写价格和花费时间
        if($_POST['price']){
            $info['price'] = $_POST['price'];
        }else{
            $this->error('请填写重货价格');
        }
        if($_POST['predict_time']){
            $info['predict_time'] = $_POST['predict_time'];
        }else{
            $this->error('请填写花费时间');
        }
        if($_POST['gently']){
            $info['gently'] = $_POST['gently'];
        }else{
            $this->error('请填写轻货价格');
        }

        //判断路线是否存在
        $CarWire = M('car_wire');
        $as = $CarWire->where($data)->find();
        if(!$as){
            $wire_id = $CarWire->add($data);
            if(false === $wire_id){
                $this->error('新增失败，请重试！');
            }
        }else{
            $wire_id = $as['wire_id'];
        }

        $info['logistics_id'] = $_POST['logistics_id'];
        $info['wire_id'] = $wire_id;

//        var_dump($info);die;
        $relationAS = M('wire_relation')->save($info);
        if($relationAS === false){
            $this->error('修改失败，请重试！');
        }
        $this->success('修改成功');
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

        $result = D('WireRelation', 'Service')->delete($_POST['id']);
        if (false === $result) {
            return $this->error('系统错误');
        }

        return $this->success("删除路线成功！",U('WireRelation/index'));
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


    /**
     * 搜索物流公司
     */
    public function searchName()
    {
        $name = I('request.name');
        $map['logistics_name'] = $name;
        $combo = M('logistics')->where($map)->find();
        $result = array('status'=>0,'message'=>'','data'=>'');
        if(empty($combo)){
            $result['message'] = '未能匹配物流公司，请重新输入！';
            $this->ajaxReturn($result);
        }
        $result['data'] = $combo['logistics_id'];
        $result['status'] = 1;
        $this->ajaxReturn($result);
    }


    /**
     *
     */
    public function addpath(){
        echo 'addpath';
        $this->display('edit');
    }
}