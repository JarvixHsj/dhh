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

        return parent::index('relation');
    }

    public function identityCheck()
    {
        if (!isset($_GET['id']) || !D('Logistics', 'Service')->existLogi($_GET['id'])) {
            return $this->errorReturn('需要编辑的查看的公司不存在！');
        }

        $row = M('Logistics')->field('id_positive, is_positive, id_reverse, is_reverse, logistics_id, logistics_name, created_at')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的物流公司不存在！');
        }
        $created_at = $row['created_at'] + 1296000;
        
        //判断认证状态
        if($row['is_positive'] == 1){
            $positive['message'] = '已认证';
        }elseif ($row['id_positive'] && $row['is_positive'] == 0) {
            $positive['message'] = '认证中(上传了图片)';
        }elseif ($row['is_positive'] == 2) {
            $positive['message'] = '认证失败';
        }else{
            $positive['message'] = '未上传图片';
        }
        if($row['is_reverse'] == 1){
            $reverse['message'] = '已认证';
        }elseif ($row['id_reverse'] && $row['is_reverse'] == 0) {
            $reverse['message'] = '认证中(上传了图片)';
        }elseif ($row['is_reverse'] == 2) {
            $reverse['message'] = '认证失败';
        }else{
            $reverse['message'] = '未上传图片';
        }
        if($created_at <= time()){
            $positive['message'] = '不需要认证(注册15天内)';
            $reverse['message'] = '不需要认证(注册15天内)';
        }

        $this->assign('positive', $positive);
        $this->assign('reverse', $reverse);
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * @return 
     */
    public function checkPositive()
    {
        if (!isset($_POST['id']) || !D('Logistics', 'Service')->existLogi($_POST['id'])) {
            return $this->errorReturn('需要编辑的物流公司不存在！');
        }
        $Model = D('Logistics');
        $row = $Model->field('id_positive, is_positive, logistics_id, logistics_name, created_at')->where('logistics_id = '.$_POST['id'])->find();
        $created_at = $row['created_at'] + 1296000;
        if($created_at <= time()){
            return $this->successReturn('注册15天内不需要认证，等过15天后再认证！');
        }

        if($row['id_positive'] == ''){
            return $this->errorReturn('该物流公司还未上传正面身份证照片');
        }elseif ($row['id_positive'] && $row['is_positive'] == 0) {
            $as = $Model->where('logistics_id = '.$_POST['id'])->setField('is_positive',1);
            if($as === false){
                return $this->errorReturn('提交审核失败，请刷新后重试！');
            }
            return $this->successReturn('提交审核成功,请刷新页面着看状态');
        }elseif ($row['is_positive'] == 1) {
            return $this->successReturn('已审核通过了，无需再次审核！');
        }else{
            return $this->errorReturn('提交审核失败，请刷新后重试！');
        }
    }


    /**
     * @return 
     */
    public function checkReverse()
    {
        if (!isset($_POST['id']) || !D('Logistics', 'Service')->existLogi($_POST['id'])) {
            return $this->errorReturn('需要编辑的物流公司不存在！');
        }
        $Model = D('Logistics');
        $row = $Model->field('id_reverse, is_reverse, logistics_id, logistics_name, created_at')->where('logistics_id = '.$_POST['id'])->find();
        $created_at = $row['created_at'] + 1296000;
        if($created_at <= time()){
            return $this->successReturn('注册15天内不需要认证，等过15天后再认证！');
        }

        if($row['id_reverse'] == ''){
            return $this->errorReturn('该物流公司还未上传反面身份证照片');
        }elseif ($row['id_reverse'] && $row['is_reverse'] == 0) {
            $as = $Model->where('logistics_id = '.$_POST['id'])->setField('is_reverse',1);
            if($as === false){
                return $this->errorReturn('提交审核失败，请刷新后重试！');
            }
            return $this->successReturn('提交审核成功,请刷新页面着看状态');
        }elseif ($row['is_reverse'] == 1) {
            return $this->successReturn('已审核通过了，无需再次审核！');
        }else{
            return $this->errorReturn('提交审核失败，请刷新后重试！');
        }
    }


    public function person_img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'logistics');
    }

}