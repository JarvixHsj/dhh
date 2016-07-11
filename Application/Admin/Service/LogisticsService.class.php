<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/25
 * Time: 21:37
 */

namespace Admin\Service;


class LogisticsService extends CommonService
{
//    public function getPagination($where, $fields, $order, $firstRow, $listRows) {
//        if($this->tpl_name == 'relation') {
//            $wireData = D('wire_relation')->alias('R')
//                ->join('dhh_car_wire C ON R.wire_id = C.wire_id')
//                ->join('dhh_region Reg ON C.wire_state = Reg.region_id')
//                ->join('dhh_region Re ON C.wire_end = Re.region_id')
//                ->join('dhh_logistics Lo ON R.logistics_id = Lo.logistics_id')
//                ->field('R.rela_id,R.logistics_id,R.predict_time,R.price,Reg.region_name as start,Re.region_name as end,Lo.logistics_name')
//                ->order('Lo.logistics_id')
//                ->select();
//            return $wireData;
//        }else{
//            return parent::getPagination($where, $fields, $order, $firstRow, $listRows);
//        }
//    }

    protected function getModelName() {
        return 'Logistics';
    }


    /**
     * 判断物流公司是否存在
     * @param  int     $id 物流id
     * @return boolean
     */
    public function existLogi($id) {
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
            ->where("logistics_id={$id}")
            ->save(array('logistics_status' => $status));

        return $result;
    }


    /**
     * 添加节点
     * @param  array $data 节点信息
     * @return array
     */
    public function add($data) {
        $Dao = $this->getD();

        if (isset($data['pid']) && !isset($data['level'])) {
            $data['level'] = $this->getNodeLevelByPid($data['pid']);
        }

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

//        if (isset($data['pid']) && !isset($data['level'])) {
//            $data['level'] = $this->getNodeLevelByPid($data['pid']);
//        }
//        if (false === ($data = $Dao->create($data))) {
//            return $this->errorResultReturn($Dao->getError());
//        }
        if (false === $Dao->where('logistics_id = '.$data['logistics_id'])->save($data)) {
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

        $Dao->startTrans(); //开启事务

        $OldInfo = $Dao->find($id); //查询出原有数据
        if (empty($OldInfo)) {     //判断数据是否存在
            return $this->errorResultReturn('您需要删除的物流公司不存在！');
        }
        if (false === $Dao->delete($id)) {      //判断是否删除成功
            return $this->errorResultReturn('系统错误！');
        }

        $map['logistics_id'] = $id;
        $messAS = M('logimess')->where($map)->delete(); //删除用户消息

        $offerAS = M('offer')->where($map)->delete();   //删除用户报价信息

        $trackAS = M('track')->where($map)->delete();   //删除用户物流信息

        $wireAS = M('wire_relation')->where($map)->delete();    //删除用户路线信息

        $vehicleAS = M('vehicle')->where($map)->delete();   //删除用户车辆信息

        $orderAS = M('orders')->where($map)->delete();      //删除用户订单
        if($messAS === false || $offerAS === false || $trackAS === false || $wireAS === false || $vehicleAS === false || $orderAS === false){
            $Dao->rollback();
            return $this->errorResultReturn('关联删除数据失败，请重试！');
        }
        $Dao->commit();

        //删除用户图片
        unlink('Public/'.$OldInfo['logistics_person_img']);
        unlink('Public/'.$OldInfo['logistics_open_img']);
        unlink('Public/'.$OldInfo['logistics_check_img']);
        unlink('Public/'.$OldInfo['logistics_way_img']);
        unlink('Public/'.$OldInfo['logistics_img']);

        return $this->resultReturn(true);
    }


    /**
     * 查询所有物流公司提供添加车辆选择
     */
    public function getAll()
    {
        $data = $this->getM()->field('logistics_id,logistics_name')->order('logistics_name desc')->select();
//        foreach($data as $key => $val){
//            if($val['logistics_name'] == null)
//            {
//                $data[$key]['logistics_name'] = 0;
//            }
//        }
        return $data;
    }





}