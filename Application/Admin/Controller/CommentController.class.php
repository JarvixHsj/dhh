<?php
namespace Admin\Controller;

/**
 * CommentController
 * 管理员信息
 */
class CommentController extends CommonController {
    /**
     * 管理员管理列表
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
        $result = $this->getAjaxList('Comment');

        $User = D('users');
        $Logi  = D('logistics');
        foreach($result['rows'] as $key => $val){
            $result['rows'][$key]['user_name'] = $User->getName($val['user_id']);
            $result['rows'][$key]['logistics_name'] = $Logi->getName($val['logistics_id']);
        }

        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 添加页
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
        	  || !D('Comment', 'Service')->existComm($_GET['id'])) {
            return $this->error('需要编辑的评论信息不存在！');
        }

        $row = M('comment')->find($_GET['id']);
        $User = D('users');
        $Logi  = D('logistics');
        $row['user_name'] = $User->getName($row['user_id']);
        $row['logistics_name'] = $Logi->getName($row['logistics_id']);
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 修改信息
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Comment', 'Service')->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('修改成功！');
    }

    /**
     * 更新信息
     * @return
     */
    public function update() {
        $CommService = D('Comment', 'Service');
        if (!isset($_POST['form'])
            || !$CommService->existAdmin($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $CommService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新信息成功！');
    }

    /**
     * 删除管理员
     * @return
     */
    public function delete() {
        var_dump($_POST);die;
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的评论不存在！');
        }

        $result = D('Comment', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除成功！");
    }
}
