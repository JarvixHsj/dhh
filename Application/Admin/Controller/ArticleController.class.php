<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:29
 */

namespace Admin\Controller;
use Think\Controller;

class ArticleController extends CommonController
{
    public function index()
    {
        return parent::index();
    }

    public function ajaxList()
    {
        $result = $this->getAjaxList('Article');
        $this->gridReturn($result['rows'], $result['total']);
    }


    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
            || !D('Article', 'Service')->existArt($_GET['id'])) {
            return $this->error('需要编辑的文章不存在！');
        }

//        $row = D('CarWire', 'Service')->getOne($_GET['id']);
        $row = D('Article')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的文章不存在！');
        }
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 更新信息
     * @return
     */
    public function update() {
        if (!isset($_POST['form']['id'])) {
            return $this->error('需要更新的文章不存在！');
        }

        if(!M('article')->find($_POST['form']['id'])){
            return $this->error('需要更新的文章不存在！');
        }
        $ArtService = D('Article', 'Service');

        $result = $ArtService->update($_POST['form']);
        if (!$result) {
            $this->error('系统出错！');
        }

        return $this->success('更新文章成功！');
    }



    public function view(){
        $id = $_GET['id'];
        $data = D('Article','Service')->view($id);
        $this->assign('row',$data);
        $this->assign('xzh',1);
        $this->display('show');
    }

    //ueditor 百度编辑器
    public function ueditor(){
        $data = new \Org\Util\Ueditor();
        echo $data->output();
    }

}