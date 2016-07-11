<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/27
 * Time: 11:37
 */

namespace Admin\Service;


class VehicleService extends commonService
{

    protected function getModelName() {
        return 'Vehicle';
    }


    /**
     * 判断车辆是否存在
     * @param  int     $id 物流id
     * @return boolean
     */
    public function existVeh($id) {
        $node = $this->getM()->find($id);
        return !empty($node);
    }


    /**
     * 查询所有物流公司提供选择
     */
    public function getLogistics()
    {
        $LogiService = D('Logistics','Service');
        return $LogiService->getAll();
    }


    /**
     * 查询车系列，并提供选择
     */

    /**
     * 添加车辆
     * @param  array $data 车辆信息
     * @return array
     */
    public function add($data) {
        $Dao = $this->getD();

        $Dao->startTrans();
        if (false === ($data = $Dao->create($data))) {
            return $this->errorResultReturn($Dao->getError());
        }
        $as = $Dao->add($data);

        if (false === $as) {
            $Dao->rollback();
            return $this->errorResultReturn('系统出错了！');
        }

        $Dao->commit();
        return $this->resultReturn(true);
    }

    /**
     * 更新节点信息
     * @return
     */
    public function update($data) {
        $Dao = $this->getD();


        if (false === ($data = $Dao->create($data))) {
            return $this->errorResultReturn($Dao->getError());
        }
        if (false === $Dao->where('vehicle_id = '.$data['vehicle_id'])->save($data)) {
            return $this->errorResultReturn('系统错误！');
        }

        return $this->resultReturn(true);
    }

    /**
     * 删除
     * @return
     */
    public function delete($id) {
        $Dao = $this->getM();
        $model = $Dao->find($id);
        if (empty($model)) {
            return $this->resultReturn(false);
        }

        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }

        return $this->resultReturn(true);
    }


}