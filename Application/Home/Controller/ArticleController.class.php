<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/18
 * Time: 17:50
 */

namespace Home\Controller;


class ArticleController extends CommonController
{

    /**
     * 公用的h5页面
     * 关于我们
    */
    public function getArticle()
    {
        $id = I('request.id');
        $data = M('article')->find($id);
        $this->assign('data',$data);
        $this->display('contact');

    }



}