<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/17
 * Time: 16:52
 */

namespace Api\Controller;

//客户端用户扩展接口（另一个UserController文件已太多行了）
class UserextendsController extends  ClientCommonController
{

    /**
     * 客户端删除消息
     * @param token 用户口令
     * @param id    string/int   删除一条或清空
     */
    public function deleteMess()
    {
        $this->checkUser();
        $result = $this->returns();

        $id = I('request.id');
        $Mess = D('message');

        if($id == 'all'){
            $where['user_id'] = $this->user_id;
        }else{
            $where['message_id'] = $id;

            $as = $Mess->find($id);
            if(!$as){
                $result['message'] = '该消息已不存在！请重试！';
                $this->getReturn($result);
            }
        }

        if(false === $Mess->where($where)->delete()){
            $result['message'] = '系统错误，请重试！';
            $this->getReturn($result);
        }

        $result['message'] = '删除成功!';
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 整车调度
     */
    public function cattle()
    {
        $result = $this->returns();

        $search     =    I('request.search');  //搜索的名称
        $_GET['p']  =    I('request.p',1);
        $start      =    I('request.start');   //起始市
        $end        =    I('request.end');   //目的地省
        $Cattle = D('Cattle')->getRelaAllCattle($search, $start, $end);

        if($Cattle['data']){
            $result['data'] = $Cattle['data'];
            $result['also'] = $Cattle['also'];
        }else{
            $result['data'] = array();
            $result['message'] = '没有匹配的数据';
        }
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 查看物流详情信息
     */
    public function lookDetails()
    {
        $result = $this->returns();

        $id     =    I('request.id');  //搜索的名称
        $data = D('logistics')->find($id);
        $data['logistics_person_img'] = imgDomain($data['logistics_person_img']);
        $data['logistics_open_img'] = imgDomain($data['logistics_open_img']);
        $data['logistics_check_img'] = imgDomain($data['logistics_check_img']);
        $data['logistics_way_img'] = imgDomain($data['logistics_way_img']);
        $data['logistics_img'] = imgDomain($data['logistics_img']);
        if(empty($data['logistics_head_img'])){
            $data['logistics_head_img'] = $data['logistics_img'];
        }
        $result['data']['info'] = $data;

        //长跑路线
        $wireData = D('wire_relation')->alias('R')
            ->join('dhh_car_wire C ON R.wire_id = C.wire_id')
            ->join('dhh_region Reg ON C.wire_state = Reg.region_id')
            ->join('dhh_region Re ON C.wire_end = Re.region_id')
            ->field('R.rela_id,R.predict_time,R.price,R.gently,Reg.region_name as start,Re.region_name as end')
            ->where('R.logistics_id = '.$id)
            ->select();
        $wireData ? $result['wire'] = $wireData : $result['wire'] = array();

        $result['status'] = 1;
        $this->getReturn($result);
    }


    /* 以下是二期接口 */


    /**
     * 上传 ／ 修改 身份证正面图片
     */
    public function setPositive()
    {
        $this->checkUser();
        $result = $this->returns();

        $info = $this->uploadImg('user');
        if($info['status'] == 1){
            $data['user_id'] = $this->user_id;
            $data['id_positive'] = $info['path']['positive'];
            if(!M('Users')->save($data)){
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
        $this->checkUser();
        $result = $this->returns();

        $info = $this->uploadImg('user');
        if($info['status'] == 1){
            $data['user_id'] = $this->user_id;
            $data['id_reverse'] = $info['path']['reverse'];
            if(!M('Users')->save($data)){
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