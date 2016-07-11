<?php
namespace Admin\Controller;

/**
 * SelectController
 * 筛选管理
 */
class SelectController extends CommonController {
    /**
     * 筛选列表
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
        $where['type_id'] = array('in','1,4,5');
        $order = 'type_id asc';
        $result = $this->getAjaxList('Select',$where,$order);
        foreach($result['rows'] as $key=>$val){
            if($val['is_area'] == 1){
                if(empty($val['select_area_end'])){
                    $result['rows'][$key]['select_area_state'] .= '以上';
                }else{
                    $result['rows'][$key]['select_area_state'] .= '-'.$val['select_area_end'];
                }
            }
        }
        $this->gridReturn($result['rows'], $result['total']);
    }


    /**
     * 添加页
     * @return
     */
    public function add() {
        $where['type_id'] = array('in','1,4,5');
        $data = M('type')->where($where)->select();
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
        	  || !D('Select', 'Service')->existAdmin($_GET['id'])) {
            return $this->error('需要编辑的管理员信息不存在！');
        }

        $row = M('select')->find($_GET['id']);
        $this->assign('row', $row);

        $where['type_id'] = array('in','1,4,5');
        $data = M('type')->where($where)->select();
        $this->assign('data',$data);

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

        if($_POST['form']['select_id']){
            $result = $this->update($_POST['form']);
        }else{
            $result = D('Select', 'Service')->add($_POST['form']);
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
        $adminService = D('Select', 'Service');
        if (!isset($_POST['form'])
            || !$adminService->existAdmin($_POST['form']['select_id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $adminService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新成功！');
    }



    /**
     * 删除管理员
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除筛选信息不存在！');
        }

        $result = D('Select', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除成功！");
    }
}
