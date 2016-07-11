<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:37
 */

namespace Admin\Service;


class CattleWireService extends commonService
{

    protected function getModelName() {
        return 'CattleWire';
    }


    /**
     * 判断物流公司是否存在
     * @param  int     $id 物流id
    * @return boolean
    */
    public function existCattleWire($id) {
        $node = $this->getM()->find($id);
        return !empty($node);
    }



    /**
     * 添加；
     * @return array
     */
    public function add($data) {
        $Dao = $this->getM();

        $as = $Dao->add($data);

        if (false === $as) {
            return false;
        }

        return $this->resultReturn(true);
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

    /**
     * 删除
     * @return
     */
    public function delete($id) {
        $Dao = $this->getM();

        $node = $Dao->getById($id);
        if (empty($node)) {
            return $this->errorReturn('您需要删除的路线不存在！');
        }
        if (false === $Dao->delete($id)) {
            return false;
        }

        return true;
    }


}