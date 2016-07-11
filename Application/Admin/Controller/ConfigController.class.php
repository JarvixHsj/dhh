<?php
/**
 * Created by PhpStorm.
 * User: SEEJOYS
 * Date: 2016/3/21
 * Time: 15:30
 */

namespace Admin\Controller;


class ConfigController extends CommonController
{

    public function index()
    {
        return parent::index();
    }


    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $result = $this->getAjaxList('Config');
        $this->gridReturn($result['rows'], $result['total']);
    }

    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('Config', 'Service')->existConfig($_GET['id'])) {
            return $this->error('需要编辑的配置信息不存在！');
        }

        $row = M('Config')->getById($_GET['id']);

        $this->assign('row', $row);
        $this->display();
    }


    /**
     * 更新信息
     * @return
     */
    public function update() {
        $ConfigService = D('Config', 'Service');
        if (!isset($_POST['form'])
            || !$ConfigService->existConfig($_POST['form']['id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $ConfigService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新配置信息成功！');
    }



}