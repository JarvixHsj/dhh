<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:37
 */

namespace Admin\Service;


class CattleService extends commonService
{

    protected function getModelName() {
        return 'Cattle';
    }


    /**
     * 判断物流公司是否存在
     * @param  int     $id 物流id
* @return boolean
*/
    public function existCattle($id) {
        $node = $this->getM()->find($id);
        return !empty($node);
    }


    /**
     * 设置物流公司状态
     * @param  int   $id     物流id
     * @param  int   $status 物流状态
     * @return mixed
     */
    public function setStatus($id, $status) {
        $result = $this->getM()
            ->where("id={$id}")
            ->save(array('status' => $status));

        return $result;
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
        $Cattle = D('Cattle');

        $node = $Cattle->getById($id);
        if (empty($node)) {
            return $this->errorReturn('您需要删除的黄牛不存在！');
        }

        $Cattle->startTrans(); //开启事务
        $cattleAS = $Cattle->delete($id);

        $wireAS = M('CattleWire')->where('cattle_id = '.$id)->delete();

        if (false === $cattleAS || false === $wireAS) {
            $Cattle->rollback();
            return false;
        }

        $Cattle->commit();
        return true;
    }





}