<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:37
 */

namespace Admin\Service;


class WireRelationService extends commonService
{

    protected function getModelName() {
        return 'WireRelation';
    }


    public function getPagination($where, $fields, $order, $firstRow, $listRows) {
        $map = array();
        if($where['logistics_name']){
            $logi = M('logistics')->field('logistics_id')->where("logistics_name = '{$where['logistics_name']}'")->find();
            $map['Lo.logistics_id'] = $logi['logistics_id'];
        }
        $Region = M('region');
        if($where['start']){
            $start = $Region->where("region_name = '{$where['start']}'")->find();
            $map['Reg.region_id'] = $start['region_id'];
        }
        if($where['end']){
            $end = $Region->where("region_name = '{$where['end']}'")->find();
            $map['Re.region_id'] = $end['region_id'];
        }
        $wireData = D('wire_relation')->alias('R')
            ->join('dhh_car_wire C ON R.wire_id = C.wire_id')
            ->join('dhh_region Reg ON Reg.region_id = C.wire_state')
            ->join('dhh_region Re ON Re.region_id = C.wire_end')
            ->join('dhh_logistics Lo ON Lo.logistics_id = R.logistics_id')
            ->field('R.rela_id,R.logistics_id,R.predict_time,R.price,R.gently,Reg.region_name as start,Re.region_name as end,Lo.logistics_name')
            ->where($map)
            ->limit($firstRow . ',' . $listRows)
            ->order('R.rela_id')
            ->select();
//        var_dump(D('wire_relation')->getLastSql());die;
        return $wireData;
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