<?php
namespace Admin\Controller;

use Common\Model;
use Common\Service;

/**
 * NodesController
 * 节点信息
 */
class NodesController extends CommonController {
    /**
     * 节点列表
     * @return
     */
    protected function ajaxList() {
        $nodeService = D('Node', 'Service');
        $nodes = $nodeService->getNodes();

        foreach ($nodes as $key => $node) {
            $nodes[$key]['type'] = $nodeService->getNodeType($node['level']);
        }

        $this->gridReturn($nodeService->mergeNodes($nodes), count($nodes));
    }

    /**
     * 切换节点状态
     * @return
     */
    public function toggleStatus() {
        $nodeService = D('Node', 'Service');
        if (!isset($_POST['id'])
            || !$nodeService->existNode($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        if (!$_POST['status']) {
            $nodeService->setStatus($_POST['id'], 1);
        } else {
            $nodeService->setStatus($_POST['id'], 0);
        }

        $info = $_POST['status'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }

    /**
     * 切换节点菜单项
     * @return
     */
    public function toggleMenu() {
        $nodeService = D('Node', 'Service');
        if (!isset($_POST['id'])
            || !$nodeService->existNode($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        if (!$_POST['menu']) {
            $nodeService->setMenu($_POST['id'], 1);
        } else {
            $nodeService->setMenu($_POST['id'], 0);
        }

        $info = $_POST['menu'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }

    /**
     * 添加页
     * @return
     */
    public function add() {
        $nodeService = D('Node', 'Service');
        $nodes = $nodeService->getNodes();

        $this->assign('nodes', $nodeService->mergeNodes($nodes));
        $this->display('edit');
    }

    /**
     * 创建节点
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Node', 'Service')->add($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加节点成功！');
    }

    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
              || !D('Node', 'Service')->existNode($_GET['id'])) {
            return $this->error('需要编辑的节点不存在！');
        }

        $row = M('Node')->getById($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的节点不存在！');
        }

        $nodeService = D('Node', 'Service');
        $nodes = $nodeService->getNodes();

        $this->assign('nodes', $nodeService->mergeNodes($nodes));
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 更新节点信息
     * @return
     */
    public function update() {
        $nodeService = D('Node', 'Service');
        if (!isset($_POST['form'])
            || !$nodeService->existNode($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $nodeService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新节点信息成功！');
    }

    /**
     * 删除节点
     * @return
     */
    public function delete() {
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的节点不存在！');
        }

        $result = D('Node', 'Service')->delete($_POST['id'][0]);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除节点成功！");
    }
}
