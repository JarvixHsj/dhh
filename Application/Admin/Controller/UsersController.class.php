<?php
namespace Admin\Controller;

/**
 * UsersController
 * 客户端信息
 */
class UsersController extends CommonController {
    /**
     * 管理列表
     * @return
     */
    public function index() {

        return parent::index();
    }

    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $result = $this->getAjaxList('Users');

        $this->gridReturn($result['rows'], $result['total']);
    }


    /**
     * 切换用户状态
     * @return
     */
    public function toggleStatus() {
        $UserService = D('Users', 'Service');
        if (!isset($_POST['id'])
            || !$UserService->existUser($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        if (!$_POST['status']) {
            $UserService->setStatus($_POST['id'], 1);
        } else {
            $UserService->setStatus($_POST['id'], 0);
        }

        $info = $_POST['status'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }


    /**
     * 添加
     * @return
     */
    public function add() {
        $this->display();
    }

    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
        	  || !D('Users', 'Service')->existUser($_GET['id'])) {
            return $this->error('需要编辑的用户信息不存在！');
        }
        $row = M('Users')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的物流公司不存在！');
        }

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

        if($_POST['form']['user_id']){
            $result = D('Users', 'Service')->update($_POST['form']);
        }else{
            $result = D('Users', 'Service')->add($_POST['form']);
        }
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加成功！');
    }

    /**
     * 更新信息
     * @return
     */
    public function update() {
        $UserService = D('Users', 'Service');
        if (!isset($_POST['form'])
            || !$UserService->existUser($_POST['form']['user_id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $UserService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新管理员信息成功！');
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的用户不存在！');
        }

        $result = D('Users', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除用户成功！");
    }

    //上传
    public function img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'user');
    }



}
