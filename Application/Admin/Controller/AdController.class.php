<?php
/**
 * Created by PhpStorm.
 * User: SEEJOYS
 * Date: 2016/7/29
 * Time: 15:30
 */

namespace Admin\Controller;


class AdController extends CommonController
{

    public function index()
    {
        return parent::index();
    }

    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $result = $this->getAjaxList('Ad');
        $this->gridReturn($result['rows'], $result['total']);
    }

    public function add() {
        $this->display('edit');
    }



    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('Ad', 'Service')->existAd($_GET['id'])) {
            return $this->error('需要编辑的广告不存在！');
        }

        $row = M('Ad')->getById($_GET['id']);

        $this->assign('row', $row);
        $this->display();
    }

        /**
     * 创建信息
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Ad', 'Service')->add($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加管理员成功！');
    }

    /**
     * 更新信息
     * @return
     */
    public function update() {
        $AdService = D('Ad', 'Service');
        if (!isset($_POST['form'])
            || !$AdService->existAd($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $AdService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新配置信息成功！');
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        if (!isset($_POST['id']['0'])) {
            $this->errorReturn('您需要删除广告不存在！');
        }

        $result = D('Ad', 'Service')->delete($_POST['id']['0']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除成功！");
    }

     public function person_img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'ad');
    }

}