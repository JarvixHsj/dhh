<?php
namespace Admin\Controller;

/**
 * OrdersController
 * 信息
 */
class OrdersController extends CommonController {
    /**
     * 管理列表
     * @return
     */
    public function index() {
        if($_POST['filter']){
            return $this->ajaxList();
            $this->display($this->tpl_name);
        }

        return parent::index();
    }

    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $where['order_is_delete'] = 0;
        $result = $this->getAjaxList('Orders',$where);
        $this->gridReturn($result['rows'], $result['total']);
    }



    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('Orders', 'Service')->exist($_GET['id'])) {
            return $this->error('需要编辑的订单信息不存在！');
        }
        $row = M('Orders')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的订单信息不存在！');
        }
        $this->assign('row', $row);
        $this->display();
    }


    public function create(){
        if (!isset($_POST['form'])) {
            return $this->error('无效的操作!');
        }

        if($_POST['form']['order_id']){
            $as = D('Orders', 'Service')->update($_POST['form']);
        }else{
            $as = D('Orders', 'Service')->add($_POST['form']);
        }

        if($as === false){
            return $this->error('操作失败，请重试！');
        }

        return $this->successReturn('操作成功！');

    }


    /**
     * 查看订单
     */
    public function view(){
        $id = $_GET['id'];
        $data = D('Orders','Service')->view($id);
        $this->assign('row',$data);
        $this->assign('xzh',1);
        $this->display('show');
    }


    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的订单不存在！');
        }

        $result = D('Orders', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        return $this->successReturn("删除成功！",U('Orders/index'));
    }

}
