<?php
namespace Admin\Controller;

/**
 * AdminsController
 * 管理员信息
 */
class AdminsController extends CommonController {
    /**
     * 管理员管理列表
     * @return
     */
    public function index() {
        $this->assign('roles', D('Role', 'Service')->getRoles(FALSE));

        return parent::index();
    }

    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $result = $this->getAjaxList('Admin');

        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 添加页
     * @return
     */
    public function add() {
        $this->assign('row', array());
        $this->assign('roles', D('Role', 'Service')->getRoles(FALSE));
        $this->display();
    }

    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
        	  || !D('Admin', 'Service')->existAdmin($_GET['id'])) {
            return $this->error('需要编辑的管理员信息不存在！');
        }

        $row = M('Admin')->getById($_GET['id']);
        if (C('SUPER_ADMIN_NAME') == $row['name']
            && !$_SESSION[C('ADMIN_AUTH_KEY')]) {
            return $this->errorReturn('您没有权限执行该操作！');
        }

        $this->assign('row', $row);
        $this->assign('roles', D('Role', 'Service')->getRoles(FALSE));
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

        $result = D('Admin', 'Service')->add($_POST['form']);
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
        $adminService = D('Admin', 'Service');
        if (!isset($_POST['form'])
            || !$adminService->existAdmin($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $adminService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新管理员信息成功！');
    }

    /**
     * 删除管理员
     * @return
     */
    public function delete() {
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的管理员不存在！');
        }

        $result = D('Admin', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除管理员成功！");
    }
}
