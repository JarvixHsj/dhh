<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/17
 * Time: 15:06
 */

namespace Api\Controller;


class AdminextendController extends LogisticsCommonController
{

    /**
     * 退出登录
     */
    public function exitregi()
    {
        $this->exitLogistics();
        $result = $this->returns();

        $result['message'] = '退出成功！';
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 物流端删除消息
     */
    public function deleteMess()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $id = I('request.id');
        $logimess = D('logimess');

        if($id == 'all'){
            $where['logistics_id'] = $this->logi_id;
        }else{
            $where['id'] = $id;

            $as = $logimess->find($id);
            if(!$as){
                $result['message'] = '该消息已不存在！请重试！';
                $this->getReturn($result);
            }
        }

        if(false === $logimess->where($where)->delete()){
            $result['message'] = '系统错误，请重试！';
            $this->getReturn($result);
        }

        $result['message'] = '删除成功!';
        $result['status'] = 1;
        $this->getReturn($result);

    }


    /**
     * 查询是否添加黄牛
     */
    public function cattleSelect()
    {
        $this->checkLogistics();
        $result = $this->returns();
        $result['status'] = 1;

        $Cattle = D('Cattle');
        $CattleWire = D('CattleWire');
        if( $info = $Cattle->where('logistics_id = '.$this->logi_id)->find()){
            $info['wire'] = $CattleWire->alias('cw')
                ->join("__REGION__ r ON r.region_id = cw.start")
                ->join('__REGION__ rg ON rg.region_id = cw.end')
                ->field('r.region_name as start, rg.region_name as end')
                ->where('cw.cattle_id = '.$info['id'])
                ->select();
            $result['data'] = $info;
            $result['result'] = 1;      //表示保存
        }else{
            $result['result'] = 0;      //表示新增
        }
        $this->getReturn($result);
    }


    /**
     * 整车调度--完善资料
     */
    public function cattlePerfect()
    {
        $this->checkLogistics();
        $result = $this->returns();
        //黄牛个人信息数据

        $data['name']       = I('request.name');    //姓名\公司名称
        $data['phone']      = I('request.phone');   //电话
        $data['address']       = I('request.address');  //地址
        $data['remark']       = I('request.remark');    //备注
        $data['logistics_id'] = $this->logi_id;
        $data['img'] = D('Logistics')->getLogiImg($this->logi_id);
        if(I('request.platenum')){                  //如果有传车牌号 表示是个人司机，如果没 即是物流司机
            $data['platenum'] = I('request.platenum');
            $data['type'] = 2;
        }else{
            $data['type'] = 1;
        }
        $Cattle = D('Cattle');
        $CattleWire = D('CattleWire');
        $Cattle->startTrans();          //开启事务
        if(false === $Cattle->create($data)){
            $this->getReturn($Cattle->getError());
        }
        if(I('request.result') == 0){   //判断是新增操作还是修改操作
            if(false === $insertId = $Cattle->add($data)){
                $result['message'] = '添加数据库失败';
                $this->getReturn($result);
            }
        }else{
            $data['id'] = I('request.id');
            if(false === $Cattle->save($data)){
                $result['message'] = '修改失败';
                $this->getReturn($result);
            }
            $insertId = I('request.id');
            $CattleWire->where('cattle_id = '.$insertId)->delete();
        }

        if(I('request.start1') && I('request.end1')){
            if(!$CattleWire->createRela($insertId, I('request.start1'), I('request.end1'))){
                $Cattle->rollback();
                $result['message'] = '数据出错1';
                $this->getReturn($result);
            }
        }
        if(I('request.start2') && I('request.end2')){
            if(!$CattleWire->createRela($insertId, I('request.start2'), I('request.end2'))){
                $Cattle->rollback();
                $result['message'] = '数据出错2';
                $this->getReturn($result);
            }
        }
        if(I('request.start3') && I('request.end3')){
            if(!$CattleWire->createRela($insertId, I('request.start3'), I('request.end3'))){
                $Cattle->rollback();
                $result['message'] = '数据出错3';
                $this->getReturn($result);
            }
        }
        if(I('request.start4') && I('request.end4')){
            if(!$CattleWire->createRela($insertId, I('request.start4'), I('request.end4'))){
                $Cattle->rollback();
                $result['message'] = '数据出错4';
                $this->getReturn($result);
            }
        }
        if(I('request.start5') && I('request.end5')){
            if(!$CattleWire->createRela($insertId, I('request.start5'), I('request.end5'))){
                $Cattle->rollback();
                $result['message'] = '数据出错5';
                $this->getReturn($result);
            }
        }

        $Cattle->commit();
        $result['status'] = 1;
        $result['message'] = '提交成功';
        $this->getReturn($result);
    }


    /**
     * 物流端修改达点
     */
    public function changeArrive()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $info['arrive_one'] = I('request.arrive_one');
        $info['arrive_two'] = I('request.arrive_two');
        $info['arrive_three'] = I('request.arrive_three');
        $info['arrive_four'] = I('request.arrive_four');
        $info['arrive_five'] = I('request.arrive_five');
        $info['arrive_six'] = I('request.arrive_six');

        if(false === $as = D('Logistics')->where('logistics_id = '.$this->logi_id)->save($info)){
            $result['status'] = 0;
            $result['data'] = (Object)array();
            $result['message'] = '参数错误，修改失败';
            $this->getRetuern($result);
        }

        $result['status'] = 1;
        $result['data'] = $info;
        $result['message'] = '修改成功';
        $this->getReturn($result);
    }


    /**
     * 二期……
     */
    /**
     * 上传 ／ 修改 身份证正面图片
     */
    public function setPositive()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $info = $this->uploadImg('logistics');
        if($info['status'] == 1){
            $data['logistics_id'] = $this->logi_id;
            $data['id_positive'] = $info['path']['positive'];
            if(!M('Logistics')->save($data)){
                $result['message'] = '上传失败！';
                $this->getReturn($result);
            }else{
                $result['status'] = 1;
                $result['message'] = '上传成功';
                $this->getReturn($result);
            }
        }
        $result['message'] = $info['message'];
        $this->getReturn($result);
    }


    /*＊
     * 上传 ／ 修改 身份证反面照片
     */
    public function setReverse()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $info = $this->uploadImg('logistics');
        if($info['status'] == 1){
            $data['logistics_id'] = $this->logi_id;
            $data['id_reverse'] = $info['path']['reverse'];
            if(!M('Logistics')->save($data)){
                $result['message'] = '上传失败！';
                $this->getReturn($result);
            }else{
                $result['status'] = 1;
                $result['message'] = '上传成功';
                $this->getReturn($result);
            }
        }
        $result['message'] = $info['message'];
        $this->getReturn($result);
    }

}