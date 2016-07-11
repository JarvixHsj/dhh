<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:37
 */

namespace Admin\Service;


class ArticleService extends commonService
{

    protected function getModelName() {
        return 'Article';
    }


    /**
     * 判断物流公司是否存在
     * @param  int     $id 物流id
    * @return boolean
    */
    public function existArt($id) {
        $node = $this->getM()->find($id);
        return !empty($node);
    }


    /**
     * 更新信息
     * @return
     */
    public function update($data) {
        $Dao = $this->getM();

        if (false === $Dao->save($data)) {
            return false;
        }

        return true;
    }



    public function view($id){
        $Article = $this->getM();
        $data = $Article->find($id);
        if(!$data){
            $this->success('要查看的文章不存在！',U('Article/index'));
        }

        return $data;
    }





}