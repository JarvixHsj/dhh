<?php
namespace Admin\Controller;

/**
 * PushController
 * 信息
 */
class PushController extends CommonController {
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
        $result = $this->getAjaxList('Push');

        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 添加页
     * @return
     */
    public function add() {
        $this->display('edit');
    }


    /**
     * 修改信息
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Push', 'Service')->add($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('修改成功！');
    }


    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的推送信息不存在！');
        }

        $result = D('Push', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除成功！");
    }
}
