<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:37
 */

namespace Admin\Service;


class CarWireService extends commonService
{

    protected function getModelName() {
        return 'CarWire';
    }


    /**
     * 判断物流公司是否存在
     * @param  int     $id 物流id
* @return boolean
*/
    public function existWire($id) {
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
            ->where("wire_id={$id}")
            ->save(array('wire_effect' => $status));

        return $result;
    }


    /**
     * 获取省级地区或者市级地区
     */
    public function getRegion($index)
    {
        $Region = M('region');
        $map['region_type'] = $index;
        if($index == 1){
            $map['parent_id'] = 1;
        }
        $data = $Region->where($map)->select();
        return $data;
    }


    /**
     * 添加；
     * @return array
     */
    public function add($data) {
        $Dao = $this->getM();

        $Dao->startTrans();
        $as = $Dao->add($data);

        if (false === $as) {
            $Dao->rollback();
//            return $this->errorResultReturn('系统出错了！');
            return false;
        }

        $Dao->commit();
        return $this->resultReturn(true);
    }

    /**
     * 更新信息
     * @return
     */
    public function update($data) {
        $Dao = $this->getM();

        if (false === $Dao->where('wire_id = '.$data['wire_id'])->save($data)) {
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