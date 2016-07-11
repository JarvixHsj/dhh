<?php
/**
 * Created by PhpStorm.
 * User: SEEJOYS
 * Date: 2016/1/25
 * Time: 21:31
 */

namespace Admin\Controller;
use Think\Controller;

class LogisticsController extends CommonController
{
    public function index()
    {
        if($_POST['filter']){
            $data = json_decode($_POST['filter']);
            $_POST['filter'] =json_encode($data);
            return $this->ajaxList();
            $this->display($this->tpl_name);
        }

        return parent::index();
    }

    public function ajaxList()
    {
        $result = $this->getAjaxList('Logistics','','logistics_id desc');
        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 切换物流状态
     * @return
     */
    public function toggleStatus() {
        $LogiService = D('Logistics', 'Service');
        if (!isset($_POST['id'])
            || !$LogiService->existLogi($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        if (!$_POST['status']) {
            $LogiService->setStatus($_POST['id'], 1);
        } else {
            $LogiService->setStatus($_POST['id'], 0);
        }

        $info = $_POST['status'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }


    public function add() {
        $LogiService = D('Logistics', 'Service');
//        $nodes = $LogiService->getNodes();
//
//        $this->assign('nodes', $LogiService->mergeNodes($nodes));
        $this->display('edit');
    }


    /**
     * 创建
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }
        if($_POST['form']['logistics_id']){
            $result = D('Logistics', 'Service')->update($_POST['form']);
        }else{
            $result = D('Logistics', 'Service')->add($_POST['form']);
        }
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加公司成功！',U('logistics/index'));
    }



    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id']) || !D('Logistics', 'Service')->existLogi($_GET['id'])) {
            return $this->error('需要编辑的物流公司不存在！');
        }

        $row = M('Logistics')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的物流公司不存在！');
        }

        $this->assign('row', $row);
        $this->display();
    }


    public function view()
    {
        $row = M('Logistics')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要查看的物流公司不存在！');
        }

        $this->assign('row', $row);
        $this->display('show');
    }


    /**
     * 删除物流公司
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的公司不存在！');
        }

        $result = D('Logistics', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        return $this->successReturn("删除公司成功！",U('logistics/index'));
    }


    /**
     * 路线汇总
     */
    public function relation()
    {
        $this->tpl_name = "relation";

//        $wireData = D('wire_relation')->alias('R')
//            ->join('dhh_car_wire C ON R.wire_id = C.wire_id')
//            ->join('dhh_region Reg ON C.wire_state = Reg.region_id')
//            ->join('dhh_region Re ON C.wire_end = Re.region_id')
//            ->join('dhh_logistics Lo ON R.logistics_id = Lo.logistics_id')
//            ->field('R.rela_id,R.logistics_id,R.predict_time,R.price,Reg.region_name as start,Re.region_name as end,Lo.logistics_name')
//            ->select();
//        $count = D('wire_relation')->alias('R')
//            ->join('dhh_car_wire C ON R.wire_id = C.wire_id')
//            ->join('dhh_region Reg ON C.wire_state = Reg.region_id')
//            ->join('dhh_region Re ON C.wire_end = Re.region_id')
//            ->join('dhh_logistics Lo ON R.logistics_id = Lo.logistics_id')
//            ->field('R.rela_id,R.logistics_id,R.predict_time,R.price,Reg.region_name as start,Re.region_name as end,Lo.logistics_name')
//            ->count();

        return parent::index('relation');

//        $result['rows'] = $wireData;
//        $result['total'] = $count;
//        $this->gridReturn($result['rows'], $result['total']);

    }


    public function person_img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'logistics');
    }

}