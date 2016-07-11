<?php
namespace Admin\Controller;

/**
 * OfferController
 * 管理员信息
 */
class OfferController extends CommonController {
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
        $result = $this->getAjaxList('Offer');
        $Order = D('orders');
        $Logi  = D('logistics');
        foreach($result['rows'] as $key => $val){
            $result['rows'][$key]['order_sn'] = $Order->getSn($val['order_id']);
            $result['rows'][$key]['logistics_name'] = $Logi->getName($val['logistics_id']);
        }
        $this->gridReturn($result['rows'], $result['total']);
    }


    /**
     * 创建信息
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = D('Offer','Service')->update($_POST['form']);

        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }
        return $this->success('修改成功！',U("offer/index"));
    }


    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id']) || !D('Offer', 'Service')->existOffer($_GET['id'])) {
            return $this->error('需要编辑的报价不存在！');
        }

        $row = M('Offer')->find($_GET['id']);
        if (empty($row)) {
            $this->error('需要编辑的报价不存在！');
        }
        $this->assign('row', $row);
        $this->display();
    }


    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您删除的报价不存在！');
        }

        $result = D('Offer', 'Service')->delete($_POST['id']);
        if (!$result) {
            return $this->error('系统错误');
        }

        $this->success("删除成功！",U('offer/index'));
    }
}
