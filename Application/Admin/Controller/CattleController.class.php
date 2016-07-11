<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/5/9
 * Time: 11:29
 */

namespace Admin\Controller;
use Think\Controller;

class CattleController extends CommonController
{
    public function index()
    {
        return parent::index();
    }

    public function ajaxList()
    {
        $result = $this->getAjaxList('Cattle','' ,'id desc');
        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 切换路线状态
     * @return
     */
    public function toggleStatus() {
        $WireService = D('Cattle', 'Service');
        if (!isset($_POST['id'])
            || !$WireService->existCattle($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        if (!$_POST['status']) {
            $WireService->setStatus($_POST['id'], 1);
        } else {
            $WireService->setStatus($_POST['id'], 0);
        }

        $info = $_POST['effect'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }


    public function add() {

        $this->display();
    }

    /**
     * 创建
     * @return
     */
    public function create() {
        $data = $_POST['form'];
        if (!$data) {
            return $this->error('参数不完整！');
        }
        $result = D('Cattle', 'Service')->add($data);
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
            || !D('Cattle', 'Service')->existCattle($_GET['id'])) {
            return $this->error('需要编辑的黄牛不存在！');
        }

        $row = D('Cattle')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的黄牛不存在！');
        }

        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 更新节点信息
     * @return
     */
    public function update() {
        $CattleService = D('Cattle', 'Service');
        if (!isset($_POST['form'])
            || !$CattleService->existCattle($_GET['id'])) {
            return $this->error('需要编辑的黄牛不存在！');
        }

        $result = $CattleService->update($_POST['form']);
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
            return $this->error('您需要删除的黄牛不存在！');
        }

        $result = D('Cattle', 'Service')->delete($_POST['id']);
        if (false === $result) {
            return $this->error('系统错误');
        }

        return $this->success("删除黄牛成功！",U('Cattle/index'));
    }


    public function person_img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'cattle');
    }

}