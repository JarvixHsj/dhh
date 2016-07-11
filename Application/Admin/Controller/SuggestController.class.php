<?php
namespace Admin\Controller;

/**
 * SuggestController
 * 给我们建议信息
 */
class SuggestController extends CommonController {
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
        $result = $this->getAjaxList('Suggest','','id desc');

        $User = D('users');
        $Logi = D('logistics');
        foreach($result['rows'] as $key=>$val){
            if($val['usertype'] == 1){
                $result['rows'][$key]['member_id'] = $User->getName($val['member_id']);
            }elseif($val['usertype'] == 2){
                $result['rows'][$key]['member_id'] = $Logi->getName($val['member_id']);
            }
        }
        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 切换状态
     * @return
     */
    public function toggleStatus() {
        $nodeService = D('Suggest', 'Service');
        if (!isset($_POST['id'])
            || !$nodeService->existSugg($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        if ($_POST['status'] == 0) {
            D('suggest')->where('id = '.$_POST['id'])->setInc('status', 1);
        } else {
            D('suggest')->where('id = '.$_POST['id'])->setDec('status');
        }

//        $info = $_POST['status'] ? '禁用成功！' : '启用成功！';
        $info = '操作成功！';
        $this->successReturn($info);
    }


    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的建议不存在！');
        }

        $result = D('Suggest', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除建议成功！");
    }
}
