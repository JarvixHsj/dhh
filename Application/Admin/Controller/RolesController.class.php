<?php
namespace Admin\Controller;

/**
 * RolesController
 * 角色信息
 */
class RolesController extends CommonController {
    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $where = array();

        $fullname = I('post.fullname');
        if(!empty($fullname))
            $where['fullname'] = $fullname;

        $result = D('Role', 'Service')->getRoles();

        $this->gridReturn($result, count($result));
    }

    /**
     * 添加角色
     * @return
     */
    public function add() {
        $this->assign('row', array('status' => 1));
        $this->assign('roles', D('Role', 'Service')->getRoles());
        $this->display('edit');
    }

    /**
     * 编辑角色信息
     * @return
     */
    public function edit() {
        $roleService = D('Role', 'Service');
        if (!isset($_GET['id']) || !$roleService->existRole($_GET['id'])) {
            return $this->error('需要编辑的角色不存在！');
        }

        $row = M('Role')->getById($_GET['id']);

        $this->assign('id', $row['id']);
        $this->assign('row', $row);
        $this->assign('roles', $roleService->getRoles());
        $this->assign('sids', $roleService->getSonRoleIds($row['id']));
        $this->display();
    }

    /**
     * 创建角色
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Role', 'Service')->addRole($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加角色成功！');
    }

    /**
     * 更新角色信息
     * @return
     */
    public function update() {
        $roleService = D('Role', 'Service');
        if (!isset($_POST['form'])
            || !$roleService->existRole($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $roleService->updateRole($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新角色信息成功！');
    }

    /**
     * 权限分配
     * @return
     */
    public function assignAccess() {
        $roleService = D('Role', 'Service');
        if (!isset($_GET['id'])
            || !$roleService->existRole($_GET['id'])) {
            return $this->errorReturn('需要分配权限的角色不存在！');
        }

        $role = M('Role')->getById($_GET['id']);
        if (0 == $role['pid']) {
            return $this->error('您无权限进行该操作！');
        }

        $access = D('Access')->relation(true)
                             ->where("role_id={$role['id']}")
                             ->select();
        $rAccess = array();
        foreach ($access as $key => $item) {
            $rAccess[$key]['val'] = "{$item['node_id']}:"
                                    . "{$item['node']['level']}:"
                                    . "{$item['node']['pid']}";
        }

        $role['access'] = json_encode($rAccess);

        $this->assign('role', $role);
        $this->assign('nodes', D('Node', 'Service')->getLevelNodes());
        $this->display('assign_access');
    }

    /**
     * 处理分配权限
     * @return
     */
    public function doAssignAccess() {
        $roleService = D('Role', 'Service');
        if (!isset($_POST['id']) || !$roleService->existRole($_POST['id'])) {
            return $this->errorReturn('需要分配权限的角色不存在！');
        }

        if (empty($_POST['access'])) {
            $_POST['access'] = array();
        }

        $result = $roleService->assignAccess($_POST['id'], $_POST['access']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        if (!empty($result['data'])) {
            return $this->successReturn($result['data']);
        }

        return $this->successReturn('分配权限成功！');
    }


    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除信息不存在！');
        }

        $result = D('Role', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除成功！");
    }
}
